<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class CompanyType extends Model
{
	protected $table = 'company_type';

	public $timestamps = false;

	public static $rules = array(
			'name'=>'required|min:2',
			'image_file'=>'required|image'
	);

	public static $editRules = array(
			'name'=>'required|min:2',
			'image_file'=>'image'
	);

	public function company()
	{
		return $this->hasMany('App\Http\Models\Company');
	}

	public static function post($p_Id, $p_Name, $p_Img)
	{
		if($p_Id == null)
			$v_Type = new CompanyType();
		else
			$v_Type = CompanyType::find($p_Id);
		$v_Type->name = $p_Name;

		if ($p_Img != null)
		{
			$v_Path = public_path() . '/images/companyTypes/';
			if (!\File::exists($v_Path))
				\File::makeDirectory($v_Path, 493, true);

			if ($p_Id != null && $v_Type->photo_url != null)
			{
				$v_OldFileName = explode('/', $v_Type->photo_url);
				$v_OldFileName = array_pop($v_OldFileName);
				\File::delete($v_Path . $v_OldFileName);
			}
			$v_Img = Image::make($p_Img);
			$v_Img->widen(50, function ($constraint){
				$constraint->upsize();
			});
			$v_Width = $v_Img->width();
			$v_Height = $v_Img->height();
			if($v_Width != $v_Height)
			{
				$v_Size = $v_Width < $v_Height ? $v_Width : $v_Height;
				$v_Img->crop($v_Size, $v_Size, floor(($v_Width - $v_Size)/2), floor(($v_Height - $v_Size)/2));
			}
			$v_ImgName =  time() . str_random(10) . '.png';
			$v_Img->encode('png')->save($v_Path . $v_ImgName);
			$v_Type->photo_url = url('/images/companyTypes/' . $v_ImgName);
		}

		$v_Type->save();
	}
}
