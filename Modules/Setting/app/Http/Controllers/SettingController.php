<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Setting\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Modules\Country\Models\Country;

class SettingController extends Controller
{
    public function index()
    {
        $country = Country::select('id', 'name')->get();
        $setting = Setting::where('id', '1')->first();
        return view('setting::index', compact('setting', 'country'));
    }

    public function create()
    {
        return view('setting::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $settings = Setting::find(1);
            $setting = $settings ?? new Setting();

            $setting->company_name = $request->company_name;
            $setting->tag_line = $request->tag_line;
            $setting->gst_number = $request->gst_number;
            $setting->pancard_number = $request->pancard_number;
            $setting->tan_number = $request->tan_number;
            $setting->email = $request->email;
            $setting->mobile = $request->mobile;
            $setting->address = $request->address;
            $setting->country_id = $request->country_id;
            $setting->state_id = $request->state_id;
            $setting->city_id = $request->city_id;

            if ($request->hasFile('logo')) {
                if ($setting->logo) {
                    @unlink(public_path('setting/logo/' . $setting->logo));
                    @unlink(public_path('setting/logo/thumbnail/' . $setting->logo));
                }

                $setting->logo = $this->uploadToPublicFolder(
                    $request->file('logo'),
                    $request->company_name,
                    'setting/logo',
                    'setting/logo/thumbnail'
                );
            }
            if ($request->hasFile('logo_dark')) {
                if ($setting->logo_dark) {
                    @unlink(public_path('setting/logo_dark/' . $setting->logo_dark));
                    @unlink(public_path('setting/logo_dark/thumbnail/' . $setting->logo_dark));
                }

                $setting->logo_dark = $this->uploadToPublicFolder(
                    $request->file('logo_dark'),
                    $request->company_name,
                    'setting/logo_dark',
                    'setting/logo_dark/thumbnail'
                );
            }

            if ($request->hasFile('favicon')) {
                if ($setting->favicon) {
                    @unlink(public_path('setting/favicon/' . $setting->favicon));
                    @unlink(public_path('setting/favicon/thumbnail/' . $setting->favicon));
                }

                $setting->favicon = $this->uploadToPublicFolder(
                    $request->file('favicon'),
                    $request->company_name,
                    'setting/favicon',
                    'setting/favicon/thumbnail'
                );
            }

            $result = $setting->save();
            Cache::forget('app_settings');

            if ($result) {
                DB::commit();
                return response()->json(['status_code' => 200, 'data' => route('setting.index'), 'message' => 'Setting added successfully.']);
            } else {
                DB::rollback();
                return response()->json(['status_code' => 403, 'message' => 'Temporary not available.']);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return response()->json(['status_code' => 500, 'message' => 'Something went wrong. Please try again.', 'error' => $e->getMessage()]);
        }
    }
    private function uploadToPublicFolder($file, $imageName, $folder, $thumbFolder)
    {
        $originalExtension = $file->getClientOriginalExtension();
        $filename = Str::slug($imageName) . '-' . time() . '.' . $originalExtension;

        $originalPath = public_path($folder);
        $thumbPath = public_path($thumbFolder);

        if (!File::exists($originalPath)) {
            File::makeDirectory($originalPath, 0755, true);
        }
        if (!File::exists($thumbPath)) {
            File::makeDirectory($thumbPath, 0755, true);
        }

        // Save original without modification
        $file->move($originalPath, $filename);

        // Generate thumbnail (always webp to save space)
        $tempPath = $originalPath . '/' . $filename;
        $src = imagecreatefromstring(file_get_contents($tempPath));

        if (!$src) return $filename;

        $trueColor = imagecreatetruecolor(200, 200);
        list($width, $height) = getimagesize($tempPath);
        imagecopyresampled($trueColor, $src, 0, 0, 0, 0, 200, 200, $width, $height);

        // Save thumbnail as webp
        $thumbName = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        imagewebp($trueColor, $thumbPath . '/' . $thumbName, 90);

        imagedestroy($src);
        imagedestroy($trueColor);

        return $filename;
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('setting::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('setting::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
