<?php

namespace App;

use App\Http\Cache\CacheRemark;
use App\Models\Document;
use App\Models\Remark;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UseRecord
{
    public static function makeRemark($request_remark, $instance)
    {
        $request_remark = implode(' ', array_filter(explode(' ', $request_remark)));

        if ($request_remark && $request_remark != $instance->remark) {
            $remark = new Remark([
                'remark' => $request_remark,
            ]);
            $instance->remarks()->save($remark);
            CacheRemark::forget();
        }
    }

    public static function documentsUpload($document_files, $instance, $additional = null)
    {
        foreach ((is_array($document_files) ? $document_files : []) as $key => $file) {
            if ($file->isValid()) {
                $folder_path = class_basename($instance).'/'.date('Y/F/');
                Storage::makeDirectory($folder_path);

                $file_name = $additional.$file->getClientOriginalName();

                $name = pathinfo($file_name, PATHINFO_FILENAME);
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $salt = Str::random(2);
                $full_path = $folder_path.'/'.$file_name;

                if (Storage::exists('documents/'.$full_path)) {
                    $full_path = $folder_path.'/'.$name.'-'.$salt.'.'.$extension;
                }

                $document_path = $file->storeAs('documents', $full_path, 'public');

                $document = new Document(['path' => $document_path]);
                $instance->documents()->save($document);

                $instance->document = $document_path;
                $instance->save();
            }
        }
    }
}
