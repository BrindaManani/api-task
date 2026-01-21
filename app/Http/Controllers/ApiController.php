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

    public function validate_product(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:products,item_id',
            'purchase_code' => 'required',
            'activated_domain' => 'required|starts_with:http://,https://',
            'version' => 'required|regex:/^\d+\.\d+(?:\.\d+)?$/',
        ]);
        try {
            $validate_product = License::where([
                ['item_id', $request->item_id],
                ['purchase_code', $request->purchase_code],
                ['activated_domain', $request->activated_domain],
            ])->whereHas('product_versions', function ($query) use ($request) {
                $query->where('pid', $request->item_id)
                    ->where('version', $request->version);
            })->first();
            if ($validate_product !== null) {
                License::updateOrCreate(
                    ['id' => $validate_product->id],
                    ['last_validate_request' => date('Y-m-d H:i:s')],
                );
                return response()->json([
                    'success' => "Validation success",
                ]);
            } else {
                return response()->json([
                    'Error' => 'Invalid Data given'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'Error' => 'Something went wrong !!'
            ], 500);
        }
    }

    public function get_active_domain(request $request)
    {
        try {
            $request->validate([
                'purchase_code' => 'required',
            ]);
            $domain = License::where('purchase_code', $request->purchase_code)->first();
            return response()->json([
                'Success' => "Active domain found",
                $domain->activated_domain,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'Error' => 'Active domain not found'
            ], 500);
        }
    }
}
