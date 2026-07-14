<?php

namespace App\Helpers;

use App\Http\Controllers\Sender;
use App\Http\Controllers\SMS\CustomSender;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Role;

class Helper
{

    public static function getAllUsers()
    {
        return User::where('active', 1)->get() ?? '[]';
    }

    public static function getAllStatus()
    {
        // if you want to change status then also change in app/console/command/statisticsrun
        $statusValues = ['death', 'moved', 'abroad', 'alive', 'unknown'];

        return $statusValues;
    }

    public static function getTablesName()
    {
        $tableNames = [];
        $dataTables = DB::table('tablelists')->get();
        foreach ($dataTables as $key => $dataTable) {
            $tableNames[] = $dataTable->value;
        }

        return $tableNames;
    }

 

    public static function getSmsUserName()
    {
        return DB::table('config')->where('name', 'user_name')->first()->value ?? '';
    }

    public static function getSmsUserPass()
    {
        return DB::table('config')->where('name', 'password')->first()->value ?? '';
    }

    public static function getSmsSenderID()
    {
        return DB::table('config')->where('name', 'sender')->first()->value ?? '';
    }

    public static function getSmsTemplate()
    {
        return DB::table('config')->where('name', 'sms_tamplate')->first()->value ?? '';
    }

    public static function getConfig()
    {
        return DB::table('config')->get();
    }

    public static function getSMSCost()
    {
        return DB::table('config')->where('name', 'per_sms_cost')->first()->value;
    }

    public static function getPrintingPressName()
    {
        return DB::table('config')->where('name', 'printing_press')->first()->value ?? '';
    }

    public static function getSMSConfiguration()
    {
        $smsConfiguration = Helper::getConfig()->firstWhere('name', 'smsConfiguration')->value ?? '';
        $jsonData = json_decode($smsConfiguration);

        return $jsonData;
    }

    public static function sendSMS($mobile, $message)
    {
        if (Helper::getConfig()->firstWhere('name', 'currentSmsProvider')->value == 'muthofun') {
            $this->sendSingleSms($mobile, $message);
        } else {
            return self::sendSMSRouteMobile($mobile, $message);
        }
        // You can return a response or view here as needed
    }

    public static function sendSMSRouteMobile($mobile, $message)
    {
        $SMSConfiguration = Helper::getSMSConfiguration();
        $sender = new Sender(
            'apibd.rmlconnect.net', //IP
            '8443', //Port
            $SMSConfiguration->routemobile->user_name, // Username
            $SMSConfiguration->routemobile->password, // Password
            $SMSConfiguration->routemobile->sender,
            $message, // Your message
            $mobile, // Mobile number
            '2', // Message type
            '1'  // DLR
        );
        // Send the SMS
        $response = $sender->Submit();

        return $response;
        // You can return a response or view here as needed
    }

    public static function sendSMSMuthofun($mobile, $message)
    {
        // API URL
        $url = 'https://sysadmin.muthobarta.com/api/v1/send-sms';

        // Create a new cURL resource
        $ch = curl_init($url);

        // Setup request to send json via POST
        $data = ['receiver' => $mobile, 'message' => $message, 'remove_duplicate' => true];
        $postdata = json_encode($data);

        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $SMSConfiguration = Helper::getSMSConfiguration();
        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'Authorization:'.$SMSConfiguration->muthofun->api_token,
        ]); //your token

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the POST request
        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public static function storeToSmsHistory($mobile, $message, $type = null, $status = null, $sms_provider = null)
    {
        // Detect if the message contains Bengali characters
        $containsBengali = preg_match('/[\x{0980}-\x{09FF}]/u', $message);

        // Determine font type
        $font_type = ($containsBengali) ? 'unicode' : 'normal';

        // Count the total number of characters
        $totalCharacters = mb_strlen($message);

        // Calculate unit based on font type
        $unit = ($font_type === 'unicode') ? ceil($totalCharacters / 70) : ceil($totalCharacters / 120);
        $name = str_replace('_', ' ', strtolower($type));
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);
        $name = lcfirst($name);
        $count = $status == 1 ? count(explode(',', $mobile)) : 0;
        SmsHistory::insert([
            'sms_type' => $type,
            'name' => $name,
            'sms_provider' => $sms_provider, //  NEW
            'mobile' => $mobile,
            'sms' => $message,
            'font_type' => $font_type,
            'characters' => $totalCharacters,
            'status' => $status,
            'count' => $count,
            'unit' => $unit,
            'created_at' => Carbon::now(),
        ]);
    }

    

    public static function getSmsHistory()
    {
        $sms_histories = [];
        $latest = DB::table('sms_balance_histories')->orderBy('id', 'desc')->first();
        if (! empty($latest)) {
            $sms_history_all = DB::table('sms_balance_histories')->get();
            $sms_histories['latest'] = $latest;
            $sms_histories['all'] = $sms_history_all;

            return $sms_histories;
        } else {
            return 'Not Found';
        }
    }

    public static function getDepositHistory()
    {
        $smsStatistics = DB::table('sms_balance_histories')->get();

        return json_decode($smsStatistics);
    }

    public static function getPerSmsCost()
    {
        $perSmsCostQuery = DB::table('config')->where('name', 'per_sms_cost')->first();
        if ($perSmsCostQuery) {
            $perSmsCost = $perSmsCostQuery->value;

            return $perSmsCost;
        } else {
            return 0;
        }
    }

    public static function getSmsStatistics()
    {

        $smsStatisticsQuery = DB::table('statistics')->where('name', 'sms_statistics')->first();
        if ($smsStatisticsQuery) {
            $smsStatistics = $smsStatisticsQuery->value;

            return json_decode($smsStatistics);
        } else {
            return [];
        }
    }

    public static function getUserStatistics($id)
    {
        $userStatistics = UserTrack::where('uid', $id)->first();

        return $userStatistics;
    }

    public static function getTeams()
    {
        $teams = DB::table('users')->where('role', 'manager')->get();

        return $teams;
    }

    public static function getTameUsers()
    {
        return DB::table('users')->whereNotNull('salt')->get();
    }

    public static function checkAvailableBalance()
    {
        $smsStatistics = json_decode(DB::table('statistics')->where('name', 'sms_statistics')->first()->value);
        if ($smsStatistics->available_balance <= 1) {
            return 0;
        } else {
            return 1;
        }
    }

    public static function smsRestriction()
    {
        $sQuery = DB::table('config')->where('name', 'sms_restriction')->first();
        if ($sQuery) {
            $data = $sQuery->value;

            return json_decode($data);
        } else {
            return [];
        }
    }

    public static function search_forntenHits()
    {
        $existing_count_found = DB::table('statistics')->where('name', 'search_count')->first();
        $today = Carbon::today()->toDateString();
        if ($existing_count_found) {
            $decode_existing_count_found = json_decode($existing_count_found->value, true);
            if (isset($decode_existing_count_found[$today]['frontendHits'])) {
                $decode_existing_count_found[$today]['frontendHits'] += 1;
            } else {
                $decode_existing_count_found[$today]['frontendHits'] = 1;
            }
            // dd($decode_existing_count_found);
            DB::table('statistics')->where('name', 'search_count')->update([
                'value' => json_encode($decode_existing_count_found),
            ]);
        } else {
            $search_count = [];
            $search_count[$today]['frontendHits'] = 1;
            $encode_search_count = json_encode($search_count);
            DB::table('statistics')->insert([
                'name' => 'search_count',
                'value' => $encode_search_count,
            ]);
        }
    }

    public static function search_backendHits()
    {
        $existing_count_found = DB::table('statistics')->where('name', 'search_count')->first();
        $today = Carbon::today()->toDateString();
        if ($existing_count_found) {
            $decode_existing_count_found = json_decode($existing_count_found->value, true);
            if (isset($decode_existing_count_found[$today]['backendHits'])) {
                $decode_existing_count_found[$today]['backendHits'] += 1;
            } else {
                $decode_existing_count_found[$today]['backendHits'] = 1;
            }
            // dd($decode_existing_count_found);
            DB::table('statistics')->where('name', 'search_count')->update([
                'value' => json_encode($decode_existing_count_found),
            ]);
        } else {
            $search_count = [];
            $search_count[$today]['backendHits'] = 1;
            $encode_search_count = json_encode($search_count);
            DB::table('statistics')->insert([
                'name' => 'search_count',
                'value' => $encode_search_count,
            ]);
        }
    }

    public static function getSmsDueBalance()
    {
        if (! empty(Helper::getSmsStatistics())) {
            $available_balance = Helper::getSmsStatistics()->available_balance;
        } else {
            $available_balance = 0;
        }

        $due = DB::table('sms_balance_histories')->where('status', 0)->sum('amount');
        if ($available_balance < 0) {
            $total_due = $due + (-$available_balance);

            return $total_due;
        } else {
            $total_due = $due;

            return $total_due;
        }
    }

    public static function getSmsLowBalance()
    {
        $exist_low_balance = DB::table('config')->where('name', 'sms_low_balance')->first();
        if (isset($exist_low_balance) || ! empty($exist_low_balance)) {
            return $exist_low_balance->value;
        } else {
            return 10000;
        }
    }

    //new sned sms
    public static function NewsendSMS($mobile, $message)
    {
        // dd($mobile);
        $newsender = new CustomSender(
            'apibd.rmlconnect.net', //IP
            '8443', //Port
            Helper::getConfig()->firstWhere('name', 'user_name')->value, // Username
            Helper::getConfig()->firstWhere('name', 'password')->value, // Password
            Helper::getConfig()->firstWhere('name', 'sender')->value,
            $message, // Your message
            $mobile, // Mobile number
            '2', // Message type
            '1'  // DLR
        );
        // Send the SMS
        $response = $newsender->Submit();

        return $response;
        // You can return a response or view here as needed
    }

    public static function numberReadyForSms($mobile)
    {
        $finalMobileString = $mobile;
        // Initialize arrays to hold the processed and invalid numbers
        $processedNumbers = [];
        $invalidNumbers = [];
        // Check if there are multiple numbers (comma-separated)
        if (strpos($finalMobileString, ',') !== false) {
            // If there are multiple numbers, split them
            $mobileNumbers = explode(',', $finalMobileString);
            foreach ($mobileNumbers as $number) {
                // Trim each number
                $trimmedNumber = trim($number);

                // Check if the number is 11 digits long after potentially removing '+88'
                $numberWithoutPrefix = str_starts_with($trimmedNumber, '+88') ? substr($trimmedNumber, 3) : $trimmedNumber;
                if (strlen($numberWithoutPrefix) == 11) {
                    // Prepend +88 if not present
                    if (! str_starts_with($trimmedNumber, '+88')) {
                        $trimmedNumber = '+88'.$numberWithoutPrefix;
                    }
                    $processedNumbers[] = $trimmedNumber;
                } else {
                    // Add to invalid numbers array
                    $invalidNumbers[] = $trimmedNumber;
                }
            }
        } else {
            // If it's a single number, process this one number
            $numberWithoutPrefix = str_starts_with($finalMobileString, '+88') ? substr($finalMobileString, 3) : $finalMobileString;
            if (strlen($numberWithoutPrefix) == 11) {
                // Prepend +88 if not present
                if (! str_starts_with($finalMobileString, '+88')) {
                    $finalMobileString = '+88'.$numberWithoutPrefix;
                }
                $processedNumbers[] = $finalMobileString;
            } else {
                // Add to invalid numbers array
                $invalidNumbers[] = $finalMobileString;
            }
        }

        return $processedNumbers;
    }

    public static function smsHistoryLog()
    {
        $all_sms_history = DB::table('sms_history')->get();

        return $all_sms_history;
    }

    public static function AdminSMSCount($mobiles, $admin_sms_type, $msg)
    {
        $json_mobiles = json_decode($mobiles);
        // dd($json_mobiles);
        $excat_number = implode(',', $json_mobiles);
        $processedNumbers = Helper::numberReadyForSms($excat_number);
        $mobilewithprefix = implode(',', $processedNumbers);
        $mobile_count = count($processedNumbers);
        $sms_response = Helper::sendSMS($mobilewithprefix, $msg, 'admin_sms');
        $existing_admin_sms = DB::table('statistics')->where('name', 'admin_sms')->first();
        $today = Carbon::today()->toDateString();
        $admin_sms = [];
        if ($existing_admin_sms) {
            $decode_existing_admin_sms = json_decode($existing_admin_sms->value, true);
            if (isset($decode_existing_admin_sms[$today][$admin_sms_type])) {
                // dd($decode_existing_admin_sms);
                $decode_existing_admin_sms[$today][$admin_sms_type] += $mobile_count;
                $decode_existing_admin_sms[$today]['total_count'] += $mobile_count;
            } else {
                $decode_existing_admin_sms[$today][$admin_sms_type] = $mobile_count;
                $decode_existing_admin_sms[$today]['total_count'] = $mobile_count;
            }
            // dd($decode_existing_admin_sms);
            DB::table('statistics')->where('name', 'admin_sms')->update([
                'value' => json_encode($decode_existing_admin_sms),
            ]);
        } else {
            $admin_sms[$today] = [$admin_sms_type => $mobile_count];
            // $admin_sms[$today]['total_count'] = 0;
            $admin_sms[$today]['total_count'] = $mobile_count;
            $encode_admin_sms = json_encode($admin_sms);
            DB::table('statistics')->insert([
                'name' => 'admin_sms',
                'value' => $encode_admin_sms,
            ]);
        }
    }

    

    public static function upload($name, $value, $directory)
    {
        ini_set('memory_limit', '30000M');
        set_time_limit(50000);

        if (! File::isDirectory(public_path($directory))) {
            File::makeDirectory(public_path($directory), 0755, true, true);
        }

        $imageName = $name.'.jpg';
        $imagePath = public_path($directory.'/'.$imageName);

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $image = Image::make($value)->orientate();

        // resize large images
        $image->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $quality = 90;

        do {
            $image->save($imagePath, $quality);
            clearstatcache();
            $size = filesize($imagePath);
            $quality -= 5;

        } while ($size > 300 * 1024 && $quality > 10);

        return [
            'image_path' => $directory.'/'.$imageName,
            'image_name' => $imageName,
        ];
    }

    // image check on folder
    public static function imageCheck($image)
    {
        $imageName = $image; // Change this to the name of your image

        if (! empty($imageName)) {
            // Check if a file with the same name exists in the directory
            $existingImagePath = public_path('medias/voter/'.$imageName);

            if (file_exists($existingImagePath)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    //get system conter
    public static function getContent()
    {
        return SystemMedia::all();
    }

    //sms teammplate
    public static function getSMSTamplate($id = null, $tableName = null)
    {
        if ($id == null && $tableName == null) {
            $sms = 'SMS template is not avialable.';
        } else {
            $voterInfo = Db::table($tableName)->where('id', $id)->first();
            $center = $voterInfo->center ?? 'কেন্দ্র এখনো ঠিক হয় নি';
            $messageTemplate = DB::table('config')->where('name', 'sms_tamplate')->first()->value;
            // Fetch the message template as an array
            $messageTemplateString = DB::table('config')->where('name', 'sms_tamplate')->first()->value;

            // Replace placeholders with actual values
            $sms = str_replace(
                ['{serial}', '{votername}', '{fathername}', '{mothername}', '{center}', '{nid}', '{website}'],
                [$voterInfo->serial, $voterInfo->name, $voterInfo->father, $voterInfo->mother, $center, $voterInfo->nid, Helper::getWebsite()],
                $messageTemplateString
            );
            // $sms = $voterInfo->serial . ". নাম: " . $voterInfo->name . ", পিতা/স্বামী: " . $voterInfo->father . " এবং কেন্দ্র: " . $center . "। বিস্তারিত জানতে ভিজিট করুন " . Helper::getWebsite();
        }

        return $sms;
    }

    // get save number option
    public static function getSaveNumberOp()
    {
        return DB::table('config')->where('name', 'save_number')->first()->value ?? 0;
    }

    public static function getShowNumberOp()
    {
        return DB::table('config')->where('name', 'voter_number_show')->first()->value ?? 0;
    }

    public static function log($event, $description = null, $origin = 'web')
    {
        $user = Auth::user();
        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->first_name.' '.$user->last_name,
            'action' => url()->current(),
            'event' => $event,
            'description' => $description,
            'origin' => $origin,
        ]);
    }

    public static function getUserLastLog($id)
    {
        return ActivityLog::where('user_id', $id)->orderby('id', 'DESC')->limit(1)->first()->description ?? 'No activity yet';
    }

    //mpdf
    public static function gen_mpdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250]]);
        /* $mpdf->AddPage('XL', '', '', '', '', 10, 10, 10, '10', '270', '');*/
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML($mpdf_view);
        $mpdf->Output($file_prefix.$file_postfix.'.pdf', 'D');
    }

    // Custom function to convert English digits to Bengali digits
    public static function banglaNumber($number)
    {
        $engNumbers = range(0, 9);
        $bangNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

        return str_replace($engNumbers, $bangNumbers, $number);
    }

    public static function roleHasPermission($role_id, $permission_id)
    {
        $found = DB::table('role_has_permissions')->where('role_id', $role_id)->where('permission_id', $permission_id)->count();

        return $found;
    }

    public static function getAllRole()
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();

        return $roles;
    }

    public static function getAuthUserRoleId()
    {
        $user = Auth::user();
        $roleId = $user->roles->first()->id ?? 0;

        return $roleId;
    }

    public static function getActiveModules()
    {
        // Read the JSON file contents
        $jsonContents = file_get_contents('modules_statuses.json');

        // Decode the JSON string into an associative array
        $data = json_decode($jsonContents, true);

        // Filter the array to get only the keys with a value of true
        $activeModules = array_keys(array_filter($data, function ($value) {
            return $value === true;
        }));

        return $activeModules;
    }

    public static function today()
    {
        return Carbon::today()->toDateString();
    }

    public static function updateUserTracking($tableName)
    {

        $today = Carbon::today()->toDateString();

        $wardServedCountQuery = UserTrack::where('uid', Auth::user()->id);

        if ($wardServedCountQuery->first()) {
            $countArray = json_decode($wardServedCountQuery->first()->count, true);
            if (isset($countArray[$today][$tableName])) {
                $countArray[$today][$tableName] += 1;
            } else {
                $countArray[$today][$tableName] = 1;
            }
            $wardServedCountQuery->update([
                'is_live' => Auth::user()->is_live,
                'geo_location' => Auth::user()->geo_location,
                'count' => json_encode($countArray),
                'total_served' => $wardServedCountQuery->first()->total_served += 1,
            ]);
        } else {
            $countArray[$today][$tableName] = 1;
            $user = Auth::user();
            $wardServedCountQuery->create([
                'uid' => $user->id,
                'team_id' => $user->team_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'designation' => $user->designation,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_live' => $user->is_live,
                'geo_location' => $user->geo_location ?? '',
                'location_updated_at' => $user->location_updated_at ?? '',
                'count' => json_encode($countArray),
                'total_served' => 1,
            ]);
        }

        return true;
    }


    /**
     * Get cached voter gender statistics from statistics table
     */
    public static function getCachedVoterGenderCount()
    {
        $row = DB::table('statistics')->where('name', 'gender_statistics')->first();

        return $row ? json_decode($row->value, true) : [];
    }

    /**
     * Get cached total centers from statistics table
     */
    public static function getCachedTotalCenters()
    {
        $row = DB::table('statistics')->where('name', 'center_statistics')->first();

        // If you stored a number, decode might not be necessary
        return $row ? json_decode($row->value, true) : 0;
    }

    /**
     * Get cached voter religion statistics from statistics table
     */
    public static function getCachedVoterReligionCount()
    {
        $row = DB::table('statistics')->where('name', 'religion_statistics')->first();

        return $row ? json_decode($row->value, true) : [];
    }

 
    public static function getTotalBoothFromBanglaString($string)
    {
        if (empty($string)) {
            return 0;
        }

        // Bangla to English digit map
        $banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        // Convert Bangla digits to English
        $convertedString = str_replace($banglaDigits, $englishDigits, $string);

        // Extract numbers
        preg_match_all('/\d+/', $convertedString, $matches);

        if (empty($matches[0])) {
            return 0;
        }

        // Sum numbers
        return array_sum(array_map('intval', $matches[0]));
    }
}
