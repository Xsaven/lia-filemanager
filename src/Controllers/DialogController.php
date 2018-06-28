<?php

namespace Lia\Filemanager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Lia\Filemanager\Traits\UploadTrait;
use Lia\Filemanager\Traits\UtilsTrait;

class DialogController extends Controller
{
    use UploadTrait, UtilsTrait;

    protected $subdir_path = '';
    protected $subdir = '';

    public function index(Request $request)
    {
        $this->use_access($request);
        session(['RF' => ['verify' => 'RESPONSIVEfilemanager']]);

        if($request->isMethod('post') && $request->submit)
            $this->upload($request);

        $this->utils($request);

        if ($request->isMethod('get') && $request->fldr) {
            $this->subdir_path = rawurldecode(trim(strip_tags($request->fldr),"/"));
        }elseif(session('RF.fldr')){
            $this->subdir_path = rawurldecode(trim(strip_tags(session('RF.fldr')),"/"));
        }
        if (strpos($this->subdir_path,'../') === FALSE
            && strpos($this->subdir_path,'./') === FALSE
            && strpos($this->subdir_path,'..\\') === FALSE
            && strpos($this->subdir_path,'.\\') === FALSE)
        {
            $this->subdir = strip_tags($this->subdir_path) ."/";
            session(['RF' => [
                'fldr' => $this->subdir_path,
                'filter' => ''
            ]]);
        }
        else { $this->subdir = ''; }

        if(empty($this->subdir))
        {
            if(!empty($request->cookie('last_position')) && strpos($request->cookie('last_position'),'.') === FALSE){
                $this->subdir = trim($request->cookie('last_position'));
            }
        }

        cookie('last_position', $this->subdir, time() + (86400 * 7));

        if ($this->subdir == "/") { $this->subdir = ""; }

        if(count(config('lia-filemanager.hidden_folders'))){
            $dirs = explode('/', $this->subdir);
            foreach($dirs as $dir){
                if($dir !== '' && in_array($dir, config('lia-filemanager.hidden_folders'))){
                    $this->subdir = "";
                    break;
                }
            }
        }

        if (config('lia-filemanager.show_total_size')) {
            list($sizeCurrentFolder,$fileCurrentNum,$foldersCurrentCount) = folder_info(config('lia-filemanager.current_path'),false);
        }

        //dd('OK!');
    }

    private function use_access(Request $request){
        if (config('lia-filemanager.USE_ACCESS_KEYS') == TRUE){
            if (!$request->akey || !count(config('lia-filemanager.access_keys'))){
                die('Access Denied!');
            }

            $request->akey = strip_tags(preg_replace( "/[^a-zA-Z0-9\._-]/", '', $request->akey));

            if (!in_array($request->akey, config('lia-filemanager.access_keys'))){
                die('Access Denied!');
            }
        }
    }

}