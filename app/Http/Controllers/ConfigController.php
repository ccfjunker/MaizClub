<?php

namespace App\Http\Controllers;

use App\Http\Models\Parameters;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;

class ConfigController extends BaseController
{
	public function getHelpItems()
	{
		return view('config.help')->with(['p_Items' => Parameters::getHelpItems()]);
	}

	public function editHelpItem($p_Id = null)
	{
		$v_Item = $p_Id == null ? null : Parameters::where('name', '=', 'help')->where('id', $p_Id)->first();
		return view('config.editHelp', array('p_Item' => $v_Item));
	}

	public function postHelpItem()
	{
		$v_Id = Input::get('id');
		if($v_Id == null)
		{
			$v_Item = new Parameters();
			$v_Item->name = 'help';
		}
		else
			$v_Item = Parameters::where('name', '=', 'help')->where('id', $v_Id)->first();

		$v_Img = Input::hasFile('image_file') ? Input::file('image_file') : null;
		if ($v_Img != null)
		{
			$v_Path = public_path() . '/images/config/';
			if (!\File::exists($v_Path))
				\File::makeDirectory($v_Path, 493, true);

			if ($v_Id != null && $v_Item->value != null)
			{
				$v_OldFileName = explode('/', $v_Item->value);
				$v_OldFileName = array_pop($v_OldFileName);
				\File::delete($v_Path . $v_OldFileName);
			}
			$v_Img = Image::make($v_Img);
			$v_ImgName =  time() . str_random(10) . '.jpg';
			$v_Img->encode('jpg')->save($v_Path . $v_ImgName);
			$v_Item->value = url('/images/config/' . $v_ImgName);
		}
		$v_Item->save();

		return redirect(url('/help'))->with('message', 'O registro foi efetuado com sucesso.');
	}


	public function deleteHelpItem($p_Id)
	{
		Parameters::where('name', '=', 'help')->where('id', $p_Id)->delete();
		return redirect(url('help'))->with('message', 'Alterações salvas!');
	}

	public function getUsageTerms()
	{
		$v_Item = Parameters::where('name', '=', 'usage_terms')->first();
		return view('config.editUsageTerms', array('p_Item' => $v_Item));
	}

	public function postUsageTerms()
	{
		$v_Item = Parameters::where('name', '=', 'usage_terms')->first();
		if($v_Item == null)
		{
			$v_Item = new Parameters();
			$v_Item->name = 'usage_terms';
		}
		$v_Item->value = Input::get('value');
		$v_Item->save();

		return redirect(url('/usage_terms'))->with('message', 'O registro foi efetuado com sucesso.');
	}

	public function getAbout()
	{
		$v_Item = Parameters::where('name', '=', 'about')->first();
		$v_Photo = Parameters::where('name', '=', 'about_photo')->first();
		return view('config.editAbout', ['p_Item' => $v_Item,
		                                 'p_Photo' => $v_Photo]);
	}

	public function postAbout()
	{
		$v_Item = Parameters::where('name', '=', 'about')->first();
		if($v_Item == null)
		{
			$v_Item = new Parameters();
			$v_Item->name = 'about';
		}
		$v_Item->value = Input::get('value');
		$v_Item->save();

		$v_PhotoItem = Parameters::where('name', '=', 'about_photo')->first();
		if($v_PhotoItem == null)
		{
			$v_PhotoItem = new Parameters();
			$v_PhotoItem->name = 'about_photo';
		}
		$v_Img = Input::hasFile('image_file') ? Input::file('image_file') : null;
		if ($v_Img != null)
		{
			$v_Path = public_path() . '/images/config/';
			if (!\File::exists($v_Path))
				\File::makeDirectory($v_Path, 493, true);

			if ($v_PhotoItem->value != null)
			{
				$v_OldFileName = explode('/', $v_PhotoItem->value);
				$v_OldFileName = array_pop($v_OldFileName);
				\File::delete($v_Path . $v_OldFileName);
			}
			$v_Img = Image::make($v_Img);
			$v_ImgName =  time() . str_random(10) . '.jpg';
			$v_Img->encode('jpg')->save($v_Path . $v_ImgName);
			$v_PhotoItem->value = url('/images/config/' . $v_ImgName);
		}
		$v_PhotoItem->save();

		return redirect(url('/about'))->with('message', 'O registro foi efetuado com sucesso.');
	}

	public function getMobileAbout()
	{
		$v_Item = Parameters::where('name', '=', 'about')->first();
		$v_Photo = Parameters::where('name', '=', 'about_photo')->first();
		return view('clients.about', ['p_Item' => $v_Item,
		                              'p_Photo' => $v_Photo]);
	}

	public function getMobileUsageTerms()
	{
		return Parameters::getUsageTerms();
	}

	public function getMobileHelp()
	{
		return view('clients.help')->with(['p_Items' => Parameters::getHelpItems()]);
	}
}

?>
