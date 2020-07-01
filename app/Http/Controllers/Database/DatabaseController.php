<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/6/19
 * Time: 15:59
 */

namespace App\Http\Controllers\Database;

use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;

class DatabaseController extends Controller
{
    public function getModel(){
        $table = trim(request('table'));

        $sql = 'SHOW FULL FIELDS FROM ' . \DB::getTablePrefix() . $table;
        $fields = \Db::select($sql);

        $soft_delete = false;
        $has_timestamps = 0;
        $guarded = array();
        $fillable = array();
        foreach($fields as $field){
            if($field->Field == 'deleted_at'){
                $soft_delete = true;
            }

            if($field->Field == 'created_at' || $field->Field == 'updated_at'){
                $has_timestamps++;
            }

            if(in_array($field->Field, array(
                'id',
                'created_at', 'updated_at', 'deleted_at',
            ))){
                $guarded[] = $field->Field;
            }else{
                $fillable[] = $field->Field;
            }
        }

        //获取表注释
        $create_table = \DB::selectOne('SHOW CREATE TABLE ' . \DB::getTablePrefix() . $table);
        //var_dump($create_table);die;
        $a = 'Create Table';
        $create_table = explode('\n', $create_table->{$a});
        $create_table_last_line = array_pop($create_table);
        preg_match('/COMMENT=\'(.*)\'/', $create_table_last_line, $table_comment);
        $table_comment = isset($table_comment[1]) ? $table_comment[1] : '';

        $class_name = StringHelper::underscore2case($table);
        $content = view('database.getModel')->with([
            'class_name'=>$class_name,
            'soft_delete'=>$soft_delete,
            'table'=>$table,
            'has_timestamps'=>$has_timestamps >= 2,
            'guarded'=>$guarded,
            'fillable'=>$fillable,
            'fields'=>$fields,
            'table_comment'=>$table_comment,
        ])->render();

        if(request('download')){
            $filename = $class_name . '.php';
            if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE){
                header('Content-Type: "application/x-httpd-php"');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header("Content-Transfer-Encoding: binary");
                header('Pragma: public');
                header("Content-Length: ".strlen($content));
            }else{
                header('Content-Type: "application/x-httpd-php"');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header("Content-Transfer-Encoding: binary");
                header('Expires: 0');
                header('Pragma: no-cache');
                header("Content-Length: ".strlen($content));
            }
            echo $content;
        }else{
            echo '<pre>', e($content);
        }
        die;
    }

    public function tables(){
        $table_prefix = \DB::getTablePrefix();

        $tables = \DB::select('SHOW TABLES');
        $table_names = [];
        foreach($tables as $table){
            $table_arr = get_object_vars($table);
            $table_name = array_shift($table_arr);
            $table_name = substr($table_name, strlen($table_prefix));
            $table_names[] = $table_name;
        }

        return view('database.tables')
            ->with('table_names', $table_names);
    }
}