<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function register_product(Request $request, $id = null)
    {
        $request->validate([
            'item_id' => 'required|exists:products,item_id',
            'activated_domain' => 'required|starts_with:http://,https://',
            'purchase_code' => 'required|unique:licenses,purchase_code,' . $id,
            'user_name' => 'required|max:255',
            'license' => 'required',
            'user_agent' => 'required',
            'ip' => 'required',
            'os' => 'required',
            'purchase_count' => 'required',
        ]);
        $item = Product::where('item_id', $request->item_id)->first();
        if (!$item) {
            return response()->json(['Error' => 'Product not found'], 404);
        }
        try {
            $license = License::updateOrCreate(
                ['id' => $id],
                [
                    'item_id' => $request->item_id,
                    'activated_domain' => $request->activated_domain,
                    'purchase_code' => $request->purchase_code,
                    'buyer' => $request->user_name,
                    'item_name' => $item->name,
                    'purchase_time' => date('Y-m-d H:i:s'),
                    'license' => $request->license,
                    'user_agent' => $request->user_agent,
                    'ip' => $request->ip,
                    'os' => $request->os,
                    'purchase_count' => $request->purchase_count,
                ],
            );
            $verification_id = ['verification_id' => "$license->item_id | $license->id | $license->item_name | $license->purchase_code"];
            return response()->json([
                'success' => true,
                'message' => 'License registered successfully',
                'data' => $verification_id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'Error' => 'Something went wrong !!'
            ], 500);
        }
        dd($request);
        return response()->json();
    }
}
