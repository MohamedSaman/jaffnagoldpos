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
     * Handle the image upload for a product.
     */
    public function upload(Request $request, $identifier)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB
        ]);

        // Try finding by ID first, then by barcode
        $product = ProductDetail::find($identifier);
        if (!$product) {
            $product = ProductDetail::where('barcode', $identifier)->first();
        }

        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        try {
            // Generate a unique filename using product code + timestamp
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = Str::slug($product->getRawOriginal('code')) . '-' . time() . '.' . $extension;

            // Create the directory if it doesn't exist
            $uploadPath = public_path('images');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Delete old image if it exists (not the default or empty)
            $oldImage = $product->getRawOriginal('image');
            $defaultImages = ['images/product.jpg', 'images/products/product.jpg', '', null];
            if ($oldImage && !in_array($oldImage, $defaultImages) && file_exists(public_path($oldImage))) {
                @unlink(public_path($oldImage));
                Log::info('Old product image deleted', ['old_image' => $oldImage]);
            }

            // Store the image in public/images
            $request->file('image')->move($uploadPath, $filename);

            // Save the relative path directly to the database (bypass accessor)
            $imagePath = 'images/' . $filename;
            DB::table('product_details')
                ->where('id', $product->id)
                ->update(['image' => $imagePath]);

            Log::info('Product image uploaded', [
                'product_id' => $product->id,
                'identifier' => $identifier,
                'image_path' => $imagePath,
            ]);

            return back()->with('success', 'Image uploaded successfully!');
        } catch (\Exception $e) {
            Log::error('Product image upload failed', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to upload image. Please try again.');
        }
    }
}
