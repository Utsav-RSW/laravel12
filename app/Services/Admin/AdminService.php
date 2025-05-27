<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminService
{
    public function login($data)
    {
        if (Auth::guard('admins')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
            if(!empty($data['remember'])) {
                setcookie("email", $data['email'], time() + 3600);
                setcookie("password", $data['password'], time() + 3600);
            }
            else {
                setcookie("email", "");
                setcookie("password", "");
            }
            $loginStatus = 1;
        }
        else {
            $loginStatus = 0;
        }

        return $loginStatus;
    }

    public function verifyPassword($data)
    {
        if(Hash::check($data['current_pwd'], Auth::guard('admins')->user()->password)) {
            return "true";
        }
        else {
            return "false";
        }
    }

    public function updatePassword($data)
    {
        // check if the Current Password is correct
        if(Hash::check($data['current_pwd'], Auth::guard('admins')->user()->password)) {
            // Check if new password and confirm password match
            if($data['new_pwd'] == $data['confirm_pwd']) {
                Admin::where('email', Auth::guard('admins')->user()->email)
                ->update(['password' => bcrypt($data['new_pwd'])]);
                $status = "success";
                $message = "Password has been updated successfully!";
            }
            else {
                $status = "error";
                $message = "New password and confirm password does not match!";
            }
        }
        else {
            $status = "error";
            $message = "Your current password is incorrect!";
        }

        return ['status' => $status, 'message' => $message];
    }

    public function updateDetails($request)
    {
        $data = $request->all();

        if($request->has('image')) {
            $image_temp = $request->file('image');
            if($image_temp->isValid()) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($image_temp);
                $extention = $image_temp->getClientOriginalExtension();
                $imageName = rand(111,99999).'.'.$extention;
                $image_path = public_path('admin/images/photos/' . $imageName);
                $image->save($image_path);
            }
        }
        elseif(!empty($data['current_image'])) {
            $imageName = $data['current_image'];
        }
        else {
            $imageName = "";
        }

        Admin::where('email', Auth::guard('admins')->user()->email)->update([
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'image' => $imageName
        ]);
    }
    
    public function deleteProfileImage($adminId)
    {
        $profileImage = Admin::where('id', $adminId)->value('image');
        if($profileImage) {
            $profile_image_path = 'admin/images/photos/' . $profileImage;
            if(file_exists($profile_image_path)) {
                unlink($profile_image_path);
            }
            Admin::where('id', $adminId)->update(['image' => null]);
            return ['status' => true, 'message' => 'Profile image has been deleted successfully!'];
        }
        return ['status' => false, 'message' => 'Profile image does not exist!'];
    }
}