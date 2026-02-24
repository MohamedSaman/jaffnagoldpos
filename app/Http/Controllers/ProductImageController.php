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

            // Create the directory if it doesn't exist
            $uploadPath = public_path('images');
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0755, true)) {
                    throw new \Exception('Failed to create upload directory');
                }
                Log::info('Created upload directory', ['path' => $uploadPath]);
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

            // Store the image in public/images
            $file = $request->file('image');
            if (!$file->move($uploadPath, $filename)) {
                throw new \Exception('Failed to move uploaded file');
            }

            Log::info('Image file moved successfully', [
                'filename' => $filename,
                'destination' => $uploadPath
            ]);

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
            $errorMessage = 'Failed to upload image: ' . $e->getMessage();

            Log::error('Product image upload failed', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            // Return appropriate response based on request type
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }
}
