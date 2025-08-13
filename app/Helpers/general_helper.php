<?php


use Modules\User\Models\UserProfile;
use Modules\Setting\Models\Setting;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Modules\Year\Models\Year;

/*hierarchy tree create start*/

if (!function_exists('getSalesTreeListUsingUserIdWeb')) {
    function getSalesTreeListUsingUserIdWeb($users)
    {
        $tree = [];
        foreach ($users as $user) {
            // Create node for the current user
            $node = [
                'user_id' => $user->user_id,  // Unique ID for the node
                'name' => $user->name,
                //'is_show' => isset($user->user_tag) ? $user->user_tag->is_show : 'no',
            ];
            $tree[] = $node;
        }
        return $tree;
    }
}
/*hierarchy tree create end*/

if (!function_exists('formatRoleName')) {
    function formatRoleName($roleName)
    {
        $words = explode(' ', $roleName);
        if (count($words) === 1) {
            return $roleName;
        }
        return implode(' ', array_map(function ($word) {
            return strtoupper(substr($word, 0, 1));
        }, $words));
    }
}

if (!function_exists('imageUploadBase64')) {
    function imageUploadBase64($data)
    {
        $file = $data['image'];
        $imageName = $data['fileName'];
        $originalStorage = $data['original_path'];
        $mediumStorage = $data['thumbnail_path'];
        explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];
        $replace = substr($file, 0, strpos($file, ',') + 1);
        $image = str_replace($replace, '', $file);
        $image = str_replace(' ', '+', $image);
        $imageFile = Str::slug($imageName) . '-' . sha1(time() . uniqid()) . '.png';
        $covertToImageFile = base64_decode($image);
        File::put($originalStorage . '/' . $imageFile, base64_decode($image));

        // $saveImages = Image::make($covertToImageFile)->insert($covertToImageFile);
        // $saveImages->resize(150, 150, function ($constraint) {
        //     $constraint->aspectRatio();
        // })->save($mediumStorage . '/' . $imageFile, 80);
        return $imageFile;
    }
}

if (!function_exists('imageUploadFromBase64')) {
    function imageUploadFromBase64($data)
    {
        $base64String   = $data['base64'];
        $imageName      = $data['fileName'] ?? '';
        $imageFolder    = rtrim($data['folder'], '/') . '/';
        $imageThumFolder = rtrim($data['thumfolder'], '/') . '/';

        if (!$base64String) {
            return null;
        }

        // Extract extension
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $extension     = strtolower($type[1]);
            $base64String  = substr($base64String, strpos($base64String, ',') + 1);
        } else {
            return null;
        }

        $decodedImage = base64_decode($base64String);
        if ($decodedImage === false) {
            return null;
        }

        // Create filename
        $filename = empty($imageName)
            ? sha1(time() . uniqid()) . '.' . $extension
            : Str::slug($imageName) . '-' . sha1(time() . uniqid()) . '.' . $extension;

        // Ensure folders exist
        Storage::makeDirectory('public/' . $imageFolder);
        Storage::makeDirectory('public/' . $imageThumFolder);

        // Save original image
        $originalPath = 'public/' . $imageFolder . $filename;
        Storage::put($originalPath, $decodedImage);

        // Thumbnail
        // $image = Image::make($decodedImage)->resize(150, 150, function ($constraint) {
        //     $constraint->aspectRatio();
        //     $constraint->upsize();
        // });

        // $thumbFilename  = 'thumb_' . $filename;
        // $thumbnailPath  = 'public/' . $imageThumFolder . $thumbFilename;
        // Storage::put($thumbnailPath, (string) $image->encode());

        return [
            'original'  => $filename,
            // 'thumbnail' => $thumbFilename,
            'original_url' => asset('storage/' . $imageFolder . $filename),
            // 'thumb_url'    => asset('storage/' . $imageThumFolder . $thumbFilename),
        ];
    }
}


// if (!function_exists('newImageUploadBase64')) {
//     function imageUploadFromBase64($data)
//     {

//         $base64String = $data['base64'];
//         $imageName = $data['fileName'] ?? '';
//         $imageFolder = rtrim($data['folder'], '/') . '/';
//         $imageThumFolder = rtrim($data['thumfolder'], '/') . '/';

//         if (!$base64String) {
//             return null;
//         }

//         // Extract extension from base64 string
//         if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
//             $extension = strtolower($type[1]); // jpg, png, webp etc.
//             $base64String = substr($base64String, strpos($base64String, ',') + 1);
//         } else {
//             return null; // Invalid image
//         }

//         $decodedImage = base64_decode($base64String);
//         if ($decodedImage === false) {
//             return null;
//         }

//         // Create filename
//         if (empty($imageName)) {
//             $filename = sha1(time() . uniqid()) . '.' . $extension;
//         } else {
//             $filename = Str::slug($imageName) . '-' . sha1(time() . uniqid()) . '.' . $extension;
//         }

//         Storage::makeDirectory('public/upload/test-folder');
//         Storage::put('public/upload/test-folder/test.txt', 'hello');


//         // Save original image
//         $originalPath = 'public/' . $imageFolder . $filename;
//         Storage::put($originalPath, $decodedImage);

//         //Generate and save thumbnail
//         $image = Image::make($decodedImage);
//         $image->resize(150, 150, function ($constraint) {
//             $constraint->aspectRatio();
//             $constraint->upsize();
//         });

//         $thumbFilename = 'thumb_' . $filename;
//         $thumbnailPath = 'public/' . $imageThumFolder . $thumbFilename;
//         Storage::put($thumbnailPath, (string) $image->encode());

//         return [
//             'original' => $filename,
//             'thumbnail' => $thumbFilename,
//         ];
//     }
// }

if (!function_exists('imageUpload')) {
    // function imageUpload($data)
    // {
    //     $originalStorage = $data['original_path'];
    //     $mediumStorage = $data['thumbnail_path'];
    //     $file = $data['image'];
    //     $imageName = $data['fileName'];

    //     $images = Image::make($file)->insert($file);
    //     if (empty($imageName)) {
    //         $filename = sha1(time() . uniqid()) . '.' . $file->getClientOriginalExtension();
    //     } else {
    //         $filename = Str::slug($imageName) . sha1(time() . uniqid()) . '.' . $file->getClientOriginalExtension();
    //     }
    //     $images->resize(150, 150, function ($constraint) {
    //         $constraint->aspectRatio();
    //     })->save($mediumStorage . '/' . $filename, 80);
    //     $file->move($originalStorage, $filename);
    //     return $filename;
    // }

    function imageUpload($data)
    {
        $file = $data['image'];
        $imageName = $data['fileName'] ?? 'image';
        $imageFolder = $data['folder'];
        $imageThumFolder = $data['thumfolder'];

        if (!$file) return null;

        $filename = Str::slug($imageName) . '-' . time() . '.webp';

        $imageFullFolder = storage_path('app/public/' . $imageFolder);
        $thumbFullFolder = storage_path('app/public/' . $imageThumFolder);

        // 🛠 Make actual folders on disk (if not exist)
        if (!File::exists($imageFullFolder)) {
            File::makeDirectory($imageFullFolder, 0755, true);
        }
        if (!File::exists($thumbFullFolder)) {
            File::makeDirectory($thumbFullFolder, 0755, true);
        }

        // Final full paths
        $imagePath = $imageFullFolder . $filename;
        $thumbPath = $thumbFullFolder . $filename;

        // Load image from temp path
        $tempPath = $file->getRealPath();
        $src = imagecreatefromstring(file_get_contents($tempPath));

        // Save original .webp
        imagewebp($src, $imagePath, 90);

        // Make 200x200 thumbnail
        $thumb = imagecreatetruecolor(200, 200);
        list($width, $height) = getimagesize($imagePath);
        imagecopyresampled($thumb, $src, 0, 0, 0, 0, 200, 200, $width, $height);
        imagewebp($thumb, $thumbPath, 90);

        imagedestroy($src);
        imagedestroy($thumb);

        return $filename;
    }
}

if (!function_exists('getSalesTreeUsingUserId')) {
    function getSalesTreeUsingUserId($user_id, $avoidCurrentUserId)
    {
        if ($avoidCurrentUserId == 'yes') {
            $userData = UserProfile::where([['parent_id', $user_id]])->select('user_id', 'parent_id')->get();
        } else {
            $userData = UserProfile::where([['user_id', $user_id]])->select('user_id', 'parent_id')->get();
        }
        return buildSalesTreeIds($userData->toArray());
    }
}

if (!function_exists('buildSalesTreeIds')) {
    function buildSalesTreeIds($users)
    {
        $ids = [];
        foreach ($users as $user) {
            $userArray = is_array($user) ? $user : $user->toArray();
            $ids[] = $userArray['user_id'];
            $children = UserProfile::select('user_id', 'parent_id')
                ->where('parent_id', '=', $userArray['user_id'])
                // ->where('user_id', '!=', $userArray['user_id'])
                ->get();
            $ids = array_merge($ids, buildSalesTreeIds($children->toArray()));
        }
        return $ids;
    }
}

// if (!function_exists('setting')) {
//     function setting()
//     {
//         return Setting::select('id', 'company_name', 'tag_line', 'favicon', 'logo', 'gst_number', 'pancard_number', 'tan_number')->first();
//     }
// }
if (!function_exists('setting')) {
    function setting()
    {
        return Cache::remember('app_settings', 60 * 60, function () {
            return Setting::select(
                'id',
                'company_name',
                'tag_line',
                'favicon',
                'logo',
                'logo_dark',
                'gst_number',
                'pancard_number',
                'tan_number'
            )->first() ?? (object) [
                'id' => null,
                'company_name' => 'Default Company',
                'tag_line' => 'Your tagline here',
                'favicon' => 'default-favicon.png',
                'logo' => 'default-logo.png',
                'logo_dark' => 'default-logo.png',
                'gst_number' => null,
                'pancard_number' => null,
                'tan_number' => null,
            ];
        });
    }
}
if (!function_exists('defaultMigration')) {
    function defaultMigration($table)
    {
        $table->softDeletes();
        $table->unsignedBigInteger('created_by')->comment('user id')->nullable();
        $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        $table->unsignedBigInteger('updated_by')->comment('user id')->nullable();
        $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        $table->unsignedBigInteger('deleted_by')->comment('user id')->nullable();
        $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
    }
}

if (!function_exists('getYear')) {
    function getYear()
    {
        $html = '';
        $years = Year::select('id', 'name', 'set_default')->get();
        if (!is_null($years)) {
            foreach ($years as $key => $value):
                if (is_null(session()->get('year'))) {
                    $class = $value->set_default == 1 ? 'active' : '';
                } else {
                    $class = session()->get('year') == $value->name ? 'active' : '';
                }
                $html .= '<a class="dropdown-item m-0 p-2 year-change ' . $class . '" data-value="' . $value->name . '">' . $value->name . '</a>';
            endforeach;
        }
        return $html;
    }
}

if (!function_exists('getSelectedYear')) {
    function getSelectedYear()
    {
        $year = Year::select('id', 'name')->where('name', session()->get('year'))->first();
        if (is_null($year)) {
            $year = Year::select('id', 'name', 'set_default')->where('set_default', 1)->first();
        }
        return $year->id;
    }
}
if (!function_exists('loginStatus')) {
    function loginStatus($row)
    {
        if (!Gate::allows('purchase-approval')) {
            return '<span class="badge bg-label-info">Active</span>';
        }
        if ($row->user->is_blocked == 0) {
            $dropDown = '<div class="dropdown">
                                <button class="btn px-2 py-1 btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Active
                                </button>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item change-status active" href="javascript:void(0);"
                                 data-id="' . $row->user->id . '" data-status="1"
                                 data-route="' . route('user-login-status-change') . '">Block</a></li>
                                 </ul>
                            </div>';
        } else {

            $dropDown = '<div class="dropdown">
                                <button class="btn px-2 py-1 btn-outline-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Block
                                </button>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item change-status active" href="javascript:void(0);"
                                 data-id="' . $row->user->id . '" data-status="0"
                                 data-route="' . route('user-login-status-change') . '">Active</a></li>
                                 </ul>
                            </div>';
        }

        return $dropDown;
    }
}
