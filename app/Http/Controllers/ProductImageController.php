<?php

namespace App\Http\Controllers;

use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    /**
     * Show the product image upload page for a given product.
     * Supports both barcode and product ID lookup.
     * URL: /product-image/{identifier}
     */
    public function show($identifier)
    {
        // Try finding by ID first (for QR code scans), then by barcode
        $product = ProductDetail::find($identifier);

        if (!$product) {
            $product = ProductDetail::where('barcode', $identifier)->first();
        }

        if (!$product) {
            return view('product-image-upload', [
                'product' => null,
                'identifier' => $identifier,
                'error' => 'No product found with identifier: ' . $identifier,
            ]);
        }

        // Load relationships for display
        $product->load(['brand', 'category', 'supplier']);

        return view('product-image-upload', [
            'product' => $product,
            'identifier' => $identifier,
            'error' => null,
        ]);
    }

    /**
     * Test endpoint to diagnose upload issues
     */
    public function testUpload()
    {
        $uploadPath = public_path('images');
        $exists = is_dir($uploadPath);
        $writable = is_writable($uploadPath);
        $permissions = fileperms($uploadPath);

        return response()->json([
            'timestamp' => now()->toIso8601String(),
            'upload_path' => $uploadPath,
            'directory_exists' => $exists,
            'directory_writable' => $writable,
            'directory_permissions' => decoct($permissions & 0777),
            'php_user' => function_exists('posix_getpwuid') ?
                posix_getpwuid(posix_geteuid())['name'] : 'unknown',
            'can_create_file' => $this->canCreateTestFile($uploadPath),
            'disk_free_space' => disk_free_space($uploadPath) . ' bytes',
            'temp_dir' => sys_get_temp_dir(),
            'temp_dir_writable' => is_writable(sys_get_temp_dir()),
        ]);
    }

    /**
     * Test if we can create a file in the upload directory
     */
    private function canCreateTestFile($path)
    {
        try {
            $testFile = $path . '/test-' . time() . '.txt';
            if (@file_put_contents($testFile, 'test')) {
                @unlink($testFile);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Handle the image upload for a product.
     */
    public function upload(Request $request, $identifier)
    {
        try {
            // Validate the uploaded image
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB
            ]);

            // Try finding by ID first, then by barcode
            $product = ProductDetail::find($identifier);
            if (!$product) {
                $product = ProductDetail::where('barcode', $identifier)->first();
            }

            if (!$product) {
                $message = 'Product not found.';
                Log::warning('Product upload attempt failed - product not found', [
                    'identifier' => $identifier
                ]);

                // Return appropriate response based on request type
                if ($request->wantsJson()) {
                    return response()->json(['error' => $message], 404);
                }
                return back()->with('error', $message);
            }

            // Generate a unique filename using product code + timestamp
            $extension = $request->file('image')->getClientOriginalExtension();
            $productCode = $product->getRawOriginal('code') ?: 'product';
            $filename = Str::slug($productCode) . '-' . time() . '.' . $extension;

            Log::info('Starting image upload', [
                'product_id' => $product->id,
                'filename' => $filename,
            ]);

            // Create the directory if it doesn't exist and ensure it's writable
            $uploadPath = public_path('images');
            
            // Step 1: Create directory if doesn't exist
            if (!is_dir($uploadPath)) {
                try {
                    if (!@mkdir($uploadPath, 0777, true)) {
                        throw new \Exception('mkdir() failed');
                    }
                    Log::info('Created upload directory', ['path' => $uploadPath]);
                } catch (\Exception $e) {
                    throw new \Exception("Failed to create upload directory: {$e->getMessage()}");
                }
            }
            
            // Step 2: Ensure directory is writable
            if (!is_writable($uploadPath)) {
                // Try to fix permissions
                try {
                    @chmod($uploadPath, 0777);
                    Log::info('Fixed directory permissions', ['path' => $uploadPath]);
                } catch (\Exception $e) {
                    Log::warning('Could not fix directory permissions', 
                        ['path' => $uploadPath, 'error' => $e->getMessage()]);
                }
                
                // Check again if writable
                if (!is_writable($uploadPath)) {
                    $perms = substr(sprintf('%o', fileperms($uploadPath)), -4);
                    throw new \Exception("Directory not writable at: {$uploadPath} (permissions: {$perms})");
                }
            }

            // Delete old image if it exists (not the default or empty)
            $oldImage = $product->getRawOriginal('image');
            $defaultImages = ['images/product.jpg', 'images/products/product.jpg', '', null];
            if ($oldImage && !in_array($oldImage, $defaultImages)) {
                $oldPath = public_path($oldImage);
                if (file_exists($oldPath)) {
                    if (@unlink($oldPath)) {
                        Log::info('Old product image deleted', [
                            'product_id' => $product->id,
                            'old_image' => $oldImage
                        ]);
                    } else {
                        Log::warning('Failed to delete old image', [
                            'old_image' => $oldImage,
                            'path' => $oldPath
                        ]);
                    }
                }
            }

            // Store the image in public/images with fallback methods
            $file = $request->file('image');
            $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $filename;
            
            $fileMoved = false;
            $moveError = '';
            
            // Method 1: Try standard move() method
            try {
                if ($file->move($uploadPath, $filename)) {
                    $fileMoved = true;
                    Log::info('Image file moved successfully (method 1)', [
                        'filename' => $filename,
                        'destination' => $uploadPath
                    ]);
                }
            } catch (\Exception $e) {
                $moveError = $e->getMessage();
                Log::warning('Method 1 (move) failed', ['error' => $moveError]);
            }
            
            // Method 2: If move failed, try copy() method
            if (!$fileMoved) {
                try {
                    $tempPath = $file->getRealPath();
                    if (@copy($tempPath, $fullPath)) {
                        $fileMoved = true;
                        Log::info('Image file copied successfully (method 2)', [
                            'filename' => $filename,
                            'destination' => $uploadPath
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Method 2 (copy) failed', ['error' => $e->getMessage()]);
                }
            }
            
            // Method 3: If copy failed, try file_put_contents()
            if (!$fileMoved) {
                try {
                    $content = file_get_contents($file->getRealPath());
                    if ($content !== false && file_put_contents($fullPath, $content) > 0) {
                        $fileMoved = true;
                        Log::info('Image file saved successfully (method 3)', [
                            'filename' => $filename,
                            'destination' => $uploadPath
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Method 3 (file_put_contents) failed', ['error' => $e->getMessage()]);
                }
            }
            
            // If all methods failed, throw error
            if (!$fileMoved) {
                $errorMsg = 'Failed to save uploaded file. ';
                $errorMsg .= 'Directory: ' . $uploadPath . ', ';
                $errorMsg .= 'Writable: ' . (is_writable($uploadPath) ? 'yes' : 'no') . ', ';
                $errorMsg .= 'Last error: ' . ($moveError ?: 'unknown');
                throw new \Exception($errorMsg);
            }
            
            // Ensure file has correct permissions
            @chmod($fullPath, 0666);

            // Save the relative path to the database
            $imagePath = 'images/' . $filename;
            $updated = DB::table('product_details')
                ->where('id', $product->id)
                ->update(['image' => $imagePath]);

            if (!$updated && $updated !== 0) {
                Log::warning('Database update may have failed', [
                    'product_id' => $product->id,
                    'imagePath' => $imagePath
                ]);
            }

            Log::info('Product image uploaded successfully', [
                'product_id' => $product->id,
                'identifier' => $identifier,
                'image_path' => $imagePath,
                'filename' => $filename,
            ]);

            $successMessage = 'Image uploaded successfully!';

            // Return appropriate response based on request type
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'image_path' => $imagePath,
                    'redirect' => $request->getRequestUri()
                ]);
            }

            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            $baseError = $e->getMessage();
            
            // Build helpful error message based on the issue
            if (strpos($baseError, 'directory not writable') !== false || 
                strpos($baseError, 'Unable to write') !== false) {
                $errorMessage = 'Directory permissions issue. ';
                $errorMessage .= 'Please ask your administrator to run: ';
                $errorMessage .= 'php artisan images:fix-permissions';
            } else {
                $errorMessage = 'Failed to upload image: ' . $baseError;
            }

            Log::error('Product image upload failed', [
                'identifier' => $identifier,
                'error' => $baseError,
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return appropriate response based on request type
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => $baseError,
                    'troubleshooting' => 'Run: php artisan images:fix-permissions'
                ], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }
}
