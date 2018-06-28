<?php

namespace Lia\Filemanager\Traits;

use Illuminate\Http\Request;
use Lia\Filemanager\Includes\FtpClient;

trait UtilsTrait
{
    public function utils(Request $request)
    {

    }

    /**
     * Delete file
     *
     * @param  string  $path
     * @param  string $path_thumb
     * @param  string $config
     *
     * @return  nothing
     */
    public function deleteFile($path, $path_thumb){
        if (config('lia-filemanager.delete_files')){
            $ftp = ftp_con();
            if($ftp){
                try{
                    $ftp->delete("/".$path);
                    @$ftp->delete("/".$path_thumb);
                }catch(FtpClient\FtpException $e){
                    return;
                }
            }else{
                if (file_exists($path)){
                    unlink($path);
                }
                if (file_exists($path_thumb)){
                    unlink($path_thumb);
                }
            }

            $info=pathinfo($path);
            if (!$ftp && config('lia-filemanager.relative_image_creation')){
                foreach(config('lia-filemanager.relative_path_from_current_pos') as $k=>$path)
                {
                    if ($path!="" && $path[strlen($path)-1]!="/") $path.="/";

                    if (file_exists($info['dirname']."/".$path.config('lia-filemanager.relative_image_creation_name_to_prepend.'.$k).$info['filename'].config('lia-filemanager.relative_image_creation_name_to_append.'.$k).".".$info['extension']))
                    {
                        unlink($info['dirname']."/".$path.config('lia-filemanager.relative_image_creation_name_to_prepend.'.$k).$info['filename'].config('lia-filemanager.relative_image_creation_name_to_append.'.$k).".".$info['extension']);
                    }
                }
            }

            if (!$ftp && config('lia-filemanager.fixed_image_creation'))
            {
                foreach(config('lia-filemanager.fixed_path_from_filemanager') as $k=>$path)
                {
                    if ($path!="" && $path[strlen($path)-1] != "/") $path.="/";

                    $base_dir=$path.substr_replace($info['dirname']."/", '', 0, strlen(config('lia-filemanager.current_path')));
                    if (file_exists($base_dir.config('lia-filemanager.fixed_image_creation_name_to_prepend.'.$k).$info['filename'].config('lia-filemanager.fixed_image_creation_to_append.'.$k).".".$info['extension']))
                    {
                        unlink($base_dir.config('lia-filemanager.fixed_image_creation_name_to_prepend.'.$k).$info['filename'].config('lia-filemanager.'.$k).".".$info['extension']);
                    }
                }
            }
        }
    }

    public function ftp_con(){
        if(config('lia-filemanager.ftp_host')){
            $ftp = new FtpClient\FtpClient();
            try{
                $ftp->connect(config('lia-filemanager.ftp_host'),config('lia-filemanager.ftp_ssl'),config('lia-filemanager.ftp_port'));
                $ftp->login(config('lia-filemanager.ftp_user'), config('lia-filemanager.ftp_pass'));
                $ftp->pasv(true);
                return $ftp;
            }catch(FtpClient\FtpException $e){
                echo "Error: ";
                echo $e->getMessage();
                echo " to server ";
                $tmp = $e->getTrace();
                echo $tmp[0]['args'][0];
                echo "<br/>Please check configurations";
                die();
            }
        }else{
            return false;
        }
    }
}