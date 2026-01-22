<?php

namespace App\Http\Controllers;

use App\Models\ApiActivity;
use App\Models\ApiRequest;
use App\Models\BuyerProfile;
use App\Models\License;
use App\Models\Product;
use App\Models\ProductVersion;
use App\Models\ResetLicenseActivityLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

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

    public function check_update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:products,item_id',
            'version' => 'required|regex:/^\d+\.\d+(?:\.\d+)?$/',
            'initial' => 'nullable|in:true,false',
        ]);
        if ($request->initial == true) {
            $install = License::where('item_id', $request->item_id)->latest()->first();
            $new = ProductVersion::where('pid', $request->item_id)->latest()->first();

            if (version_compare($request->version, $new->version, '<')) {

                return response()->json([
                    'Success' => true,
                    'message' => "Update available",
                    $data = [
                        'current_version' => $install->installed_version,
                        'latest_version' => $new->version,
                        'has_sql_update' => true,
                        'release_date' => $new->created_at,
                        'changelog' => $new->changelog,
                        'summary' => $new->summary,
                    ],
                ]);
            } else {
                $data = [
                    'current_version' => $install->installed_version,
                    'latest_version' => $new->version,
                ];
                return response()->json([
                    'Success' => true,
                    'message' => "No Update available",
                    $data,
                ]);
            }
        } else {
            $previousVersion = ProductVersion::where('pid', $request->item_id)
                ->latest()
                ->offset(1)
                ->first();
            return response()->json([
                $previousVersion,
            ]);
        }
    }

    public function reset_license(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:products,item_id',
            'purchase_code' => 'required',
        ]);
        try {
            $license = ResetLicenseActivityLog::where('purchase_code', $request->purchase_code)->firstOrFail();

            $records = ResetLicenseActivityLog::where('purchase_code', $request->purchase_code)
                ->whereBetween('reset_license_time', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])->exists();
            if ($license != null && $records == false) {
                $time = ResetLicenseActivityLog::updateOrCreate(
                    ['purchase_code' => $request->purchase_code],
                    ['reset_license_time' => date('Y-m-d H:i:s')],
                );
                return response()->json([
                    'message' => "Reset License time updated succefully",
                ]);
            } else {
                return response()->json([
                    'Error' => "Can not reset License within the week",
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'Error' => "Can not find data",
            ], 404);
        }
    }

    public function buyers($id = null)
    {
        try {
            if ($id != null) {
                $name = BuyerProfile::where('id', $id)->first();
                $buyer = License::where('buyer', $name->envato_username)->get();
                if ($buyer) {
                    return response()->json([
                        'Buyer ID' => $name->id,
                        'data' => $buyer
                    ]);
                }
            }
            $buyer = License::paginate(10);
            return response()->json([
                $buyer
            ]);
        } catch (Exception $e) {
            return response()->json([
                'Error' => "Buyer id not found"
            ], 404);
        }
    }

    public function api_requests(Request $request)
    {
        try {
            $api_request = ApiRequest::where('purchase_code', $request->purchase_code)->where('domain', $request->domain)->get();
            if (count($api_request) > 0) {
                return response()->json([
                    $api_request
                ]);
            } else {
                throw new \Exception();
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'Error' => "Data not found"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'Error' => "Data not found"
            ], 404);
        }
    }

    public function api_activities(Request $request)
    {
        try {
            if ($request->has('purchase_code') && $request->has('domain') && $request->has('item_id')) {
                $activities = ApiActivity::where('purchase_code', $request->purchase_code)->where('domain', $request->domain)->where('item_id', $request->item_id)->get();
                if (count($activities) > 0) {
                    return response()->json([
                        $activities
                    ]);
                }
            }

            if ($request->has('event_type')) {
                $filter = ApiActivity::query();
                $filtered_activity = $filter->where('event_type', 'LIKE', "%{$request->event_type}%")->paginate(10);
                return response()->json([
                    $filtered_activity
                ]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'Error' => "Data not found"
            ], 404);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'Error' => "Data not found"
            ], 404);
        }
    }

    public function download_update_sql(Request $request, $vid)
    {
        try {
            $license = License::where('purchase_code', $request->license_code)
                ->where('buyer', $request->client_name)
                ->where('activated_domain', $request->activated_domain)->get();
            $product = ProductVersion::where('vid', $vid)->get();
            if (count($license) > 0 || count($product) > 0) {
                return response()->json([
                    'Product detail' => $product,
                    'License detail' => $license
                ]);
            } else {
                throw new \Exception();
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'Error' => "Data not found"
            ], 404);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'Error' => "Data not found"
            ], 404);
        }
    }
}
