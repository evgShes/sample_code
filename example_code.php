<?php

class MC
{

    public $translit = array(

        'а' => 'a', 'б' => 'b', 'в' => 'v',

        'г' => 'g', 'д' => 'd', 'е' => 'e',

        'ё' => 'yo', 'ж' => 'zh', 'з' => 'z',

        'и' => 'i', 'й' => 'j', 'к' => 'k',

        'л' => 'l', 'м' => 'm', 'н' => 'n',

        'о' => 'o', 'п' => 'p', 'р' => 'r',

        'с' => 's', 'т' => 't', 'у' => 'u',

        'ф' => 'f', 'х' => 'x', 'ц' => 'c',

        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh',

        'ь' => '\'', 'ы' => 'y', 'ъ' => '\'\'',

        'э' => 'e\'', 'ю' => 'yu', 'я' => 'ya',


        'А' => 'A', 'Б' => 'B', 'В' => 'V',

        'Г' => 'G', 'Д' => 'D', 'Е' => 'E',

        'Ё' => 'YO', 'Ж' => 'Zh', 'З' => 'Z',

        'И' => 'I', 'Й' => 'J', 'К' => 'K',

        'Л' => 'L', 'М' => 'M', 'Н' => 'N',

        'О' => 'O', 'П' => 'P', 'Р' => 'R',

        'С' => 'S', 'Т' => 'T', 'У' => 'U',

        'Ф' => 'F', 'Х' => 'X', 'Ц' => 'C',

        'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SHH',

        'Ь' => '\'', 'Ы' => 'Y\'', 'Ъ' => '\'\'',

        'Э' => 'E\'', 'Ю' => 'YU', 'Я' => 'YA',

    );
//    public $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ');
//    public $lat = array('a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya', ' ');

    function dateConvert($date = '')
    {
        if (!empty($date)) {
            $month = [
                '01' => 'января',
                '02' => 'февраля',
                '03' => 'марта',
                '04' => 'апреля',
                '05' => 'мая',
                '06' => 'июня',
                '07' => 'июля',
                '08' => 'августа',
                '09' => 'сентября',
                '10' => 'октября',
                '11' => 'ноября',
                '12' => 'декабря'
            ];

            $data = date('d ' . $month[date('m')] . ' Y H:i', strtotime($date));
        }

        return $date;
    }

    public function transRu_trait($value = null)
    {
        if (!empty($value)) {
            return strtr($value, array_flip($this->translit));
        }
    }

    public function transLat_trait($value = null)
    {
        if (!empty($value)) {
            return strtr($value, ($this->translit));
        }
    }

    /**
     * Построение дерева категорий
     * @param $categories
     * @param int $lvl
     * @return array
     */
    public function getTree($categories, $lvl = 0)
    {
        $curr_cat = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $lvl) {
                $unit_cat['id'] = $category['own_id'];
                $unit_cat['category'] = $category['category'];
                $unit_cat['parent_id'] = $category['parent_id'];

                $nodes = $this->getTree($categories, $category['own_id']);
                if (!empty($nodes)) $unit_cat['nodes'] = $nodes;
                $curr_cat[] = $unit_cat;
                unset($unit_cat);
            }
        }
        return $curr_cat;
    }

    /*******************************************CRUD**************************************************/

    public function saveRecord($model, $input, $rules = [])
    {
        $input = $this->convertArrayRules($input, $rules, true);
        $model = new $model;
        $model->fill($input);
        $model->save();
        return $model;
    }


    /**
     * @param $input
     * @param null $model_path
     * @param null $currently_table_name
     * @return mixed
     */
    public function SaveNewRecord_trait($input, $model_path = null)
    {
        if (!empty($input)) {
            foreach ($input as $keyI => $valI) {
                if (is_array($valI)) {
                    unset($input[$keyI]);
                }
            }
        }
        if (empty($model_path)) {
            $model = (isset($this->configCont['model_name'])) ? $this->configCont['model_name'] : null;
        } else {
            $model = (strripos($model_path, "\\") === false) ? "App\\" . $model_path : $model_path;
        }
        $model_instance = new $model;
        $currently_table_name = $model_instance->getTable();
        $model_fillable = $model_instance->getFillable();
        if (!empty($input)) {
            if (isset($model::$dateFields) && sizeof($model::$dateFields) > 0) {
                $input = $this->date_format_val($input, "Y-m-d", $model::$dateFields);
            }
            $arr_val = [];
            foreach ($input as $key => $val) {
                if (in_array($key, $model_fillable)) {
                    if (!empty($val)) {
                        $arr_val[$key] = $val;
                    }
                }
            }
            if (!empty($arr_val)) {
                $query = $model::firstOrCreate($arr_val);
                return $query;
            }
        }
    }


    public function SaveOrNewRecord_trait($input, $new_value_array = null, $model_path = null)
    {
        if (!empty($new_value_array)) {
            $input = array_merge($input, $new_value_array);
        }
        if (!empty($input)) {
            foreach ($input as $keyI => $valI) {
                if (is_array($valI)) {
                    unset($input[$keyI]);
                }
            }
        }
        if (empty($model_path)) {
            $model = (isset($this->configCont['model_name'])) ? $this->configCont['model_name'] : null;
        } else {
            $model = (strripos($model_path, "\\") === false) ? "App\\" . $model_path : $model_path;
        }
        $model_instance = new $model;
        $currently_table_name = $model_instance->getTable();
        $model_fillable = $model_instance->getFillable();
        if (!empty($input)) {
            if (isset($model::$dateFields) && sizeof($model::$dateFields) > 0) {
                $input = $this->date_format_val($input, "Y-m-d", $model::$dateFields);
            }
            $arr_val = [];
            foreach ($input as $key => $val) {
                if (in_array($key, $model_fillable)) {
                    if (!empty($val)) {
                        $arr_val[$key] = $val;
                    }
                }
            }
            if (!empty($arr_val)) {
                $query = $model::firstOrNew($arr_val);
                return $query;
            }
        }
    }


    public function SearchRecord_trait($model = null, $array_val = null, $array_not_like = null)
    {
        if (!empty($array_val)) {
            if (!is_object($model)) {
                if (strripos($model, "\\") === false) $model = "App\\" . $model;
                $model = new $model;
            }
            if (isset($model::$dateFields) && sizeof($model::$dateFields) > 0) {
                $array_val = $this->date_format_val($array_val, "Y-m-d", $model::$dateFields);
            }
            $query = $model::where(function ($q) use ($array_val, $array_not_like, $model) {
                foreach ($array_val as $key => $val) {
                    if (!empty($val) && $model->isFillable($key)) {
                        $val = ($this->clean($val));
                        if (!empty($array_not_like) && in_array($key, $array_not_like)) {
                            $q->where($key, $val);
                        } else {
                            $q->where($key, 'ILIKE', "%$val%");
                        }
                    }
                };
            });
            return $query;
        }
    }

    /**
     * @param null $model
     * @param null $id_record_dupl
     * @param null $array_new_value
     * @return mixed
     */
    public function DuplicateRecord_trait($model = null, $id_record_dupl = null, $array_new_value = null)
    {
        if (!empty($id_record_dupl)) {
            if (strripos($model, "\\") === false) $model = "App\\" . $model;
            $duplicate_record = $model::find($id_record_dupl)->replicate()->fill($this->clean($array_new_value))->toArray();
            $record = $this->SaveNewRecord_trait($duplicate_record);
            return $record;
        }
    }

    /**
     * @param $input
     * @param $id
     * @param null $model
     * @return mixed
     */
    public function SaveChangesRecords_trait($input, $model_path = null, $id = null)
    {
        if (isset($input) && !empty($input)) {
            array_walk($input, function ($val, $key) use (&$input) {
                if (is_array($val)) {
                    unset($input[$key]);
                }
            });
            if (empty($model_path) && isset($this->configCont['model_name'])) {
                $model = $this->configCont['model_name'];
            } else {
                $model = (strripos($model_path, "\\") === false) ? "App\\" . $model_path : $model_path;
            }
            $model_instance = new $model;
            $currently_table_name = $model_instance->getTable();
            $model_fillable = $model_instance->getFillable();
            $id = (empty($id)) ? $input['id'] : $id;
            if (isset($model::$dateFields) && sizeof($model::$dateFields) > 0) {
                $input = $this->date_format_val($input, "Y-m-d", $model::$dateFields);
            }
            if (isset($model::$checkboxFields) && sizeof($model::$checkboxFields) > 0) {
                $input = $this->checkbox_fields($input, $model::$checkboxFields);
            }
            $record = $model::find($id);
            foreach ($input as $key => $val) {
                if (in_array($key, $model_fillable)) {
                    if ($record->$key != $val && $val != "") {
                        $record->$key = $val;
                    } elseif ($val == "") {
                        $record->$key = null;
                    }
                }
            }
            return $record;
        }
    }

    /**
     * @param array $input
     * @param array $entity_prefixes
     * @return array
     */

    public static function splitArrayByArrayPrefix($input = [], array $entity_prefixes = [])
    {
        if (!empty($input) && !empty($entity_prefixes)) {
            // проходим префиксы
            foreach ($entity_prefixes as $prefix) {
                // проходим request
                foreach ($input as $key => $val) {
                    if (preg_match("/^$prefix.*/u",$key) && is_array($val)) {
                        // проходим массив в request'е
                        foreach ($val as $k => $v) {
                            $input[$prefix][$k][$key] = $v;
                            unset($input[$key]);
                        }
                    }
                }
            }
            return $input;
        }
        return $input;
    }

    /**
     * Работает с связями
     * @param $relation
     * @param $related_primary_key
     * @param $records
     * @return mixed
     */
    protected function updateOrCreateOrDestroyMany($relation, $related_primary_key, $records = [], $add_array = [])
    {
        $id = [];
        foreach ($records as $record) {
            $rel = clone $relation;
            $record = array_merge($record, $add_array);
            $instance = $rel->firstOrNew(['id' => array_get($record, $related_primary_key)]);
            $instance->fill($record);
            $instance->save();
            $id[] = $instance->id;
        }

        $diff_id = $relation->get()->pluck('id')->diff($id)->toArray();

        if (!empty($diff_id)) {
            $destroy_count = $relation->getRelated()->destroy($diff_id);
        }
        return $relation->get();
    }

//////////END CRUD///////////

    /**
     * @param $input
     * @return array|mixed|string
     */
    public function clean($input)
    {
        $cl = function ($val) {
            $val = trim($val);
            $val = stripslashes($val);
            $val = strip_tags($val);
            $val = preg_replace('/\s{2,}/', ' ', $val);
//            $val = htmlspecialchars($val);
            return $val;
        };
        if (is_array($input)) {
            array_walk_recursive($input, function (&$item, $key) use ($cl) {
                if ($item == '' || $item == 'undefined') {
                    $item = null;
                } else {
                    $item = $cl($item);
                }
                return $item;
            });
        } elseif (is_string($input)) {
            $input = $cl($input);
        }
        return $input;
    }

    /*******************date_format_val*****************************************/
    public function date_format_val($value, $format, $name_fields_date = null)
    {
        if (is_string($value) && empty($name_fields_date)) {
            (!empty($value)) ? $value = date_create($value)->format($format) : $value = null;
            return $value;
        }
        //Если пришел массив
        if (is_array($value) && !empty($name_fields_date)) {
            if (is_array(current($value))) {
                foreach ($value as $key1 => $val1) {
                    foreach ($name_fields_date as $ke3 => $val3) {
                        if (array_key_exists($val3, $val1)) {
                            $date_val = $val1[$val3];
                            if (!empty($date_val)) {
                                $value[$key1][$val3] = date($format, strtotime($date_val));
                            } else {
                                $value[$key1][$val3] = null;;
                            }
                        }
                    }
                }
                return $value;
            } else {
                if (is_array($name_fields_date) && sizeof($name_fields_date) > 0) {
                    foreach ($name_fields_date as $key => $val) {
                        if (array_key_exists($val, $value)) {
                            $date_val = $value[$val];
                            if (!empty($date_val)) {
                                $date_val = date_create($date_val);
                                if ($date_val) {
                                    $date_val = $date_val->format($format);
                                    $value[$val] = $date_val;

                                } else {
                                    $value[$val] = null;
                                }
                            } else {
                                $value[$val] = null;
                            }
                        }
                    }
                    return $value;
                }
            }
        }
        //Если пришел объект
        if (is_object($value) && is_array($name_fields_date) && sizeof($name_fields_date) > 0) {
            foreach ($name_fields_date as $key => $val) {
                if (!empty($value->$val)) {
                    $date_val = $value->$val;
                    $date_val = date_create($date_val)->format($format);
                    $value->$val = $date_val;
                }
            }
            return $value;
        }
    }

    public function checkbox_fields($input = null, $field = null)
    {
        if (!empty($input) && !empty($field) && is_array($input)) {
            $table_name_real = $this->configCont['table_name_real'];
            foreach ($field as $key => $val) {
                if (Schema::hasColumn($table_name_real, $val)) {
                    if (!isset($input[$val])) {
                        $input[$val] = 0;
                    }
                }
            }
        }
        return $input;
    }


    /**
     * //Сохраняет отношения не сохраняя изменения в полях
     * @param $model
     * @param $array_value
     * @param null $arr_static
     */
    public function SaveRelations_trait($model, $array_value, $arr_static = null) //Сохраняет отношения не сохраняя изменения в полях
    {
        if (!empty($array_value) && !empty(current($array_value))) {
            if (strripos($model, "\\") === false) $model = "App\\" . $model;
            $model_exemp = new $model;
            $fillable = $model_exemp->getFillable();
            $arr_id = [];
            $get_keys_relat = function ($input_arr = null, $model = null) {
                if (!empty($input_arr) && !empty($model)) {
                    foreach ($input_arr as $keys => $values) {
                        if ($model->isFillable($keys)) {
                            return array_keys($values);
                        }
                    }
                }
            };

            $key_input = $get_keys_relat($array_value, $model_exemp);

            if (!empty($key_input)) {
                // Получаем ключи массивов
                foreach ($key_input as $key) {
                    foreach ($array_value as $key1 => $value) {  //Перебираем массив со значениями
                        if ($model_exemp->isFillable($key1)) {
                            if (!empty($value)) {     //Если не пустое значение в массиве
                                if (array_key_exists($key, $value)) {    //Проверям существует ли ключь в массиве значений
                                    if (!empty($value[$key])) {

                                        $arr_create[$key1] = $value[$key];
                                    } else {

                                        $arr_create[$key1] = null;
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($arr_create)) {  //Если есть новые записи создаем их!
                        if (!empty($arr_static)) $arr_create = array_merge($arr_static, $arr_create);
                        if (isset($model::$dateFields) && sizeof($model::$dateFields) > 0) {
                            $arr_create = $this->date_format_val($arr_create, "Y-m-d", $model::$dateFields);
                        }
                        $query = $model::firstOrCreate($arr_create);
                        $arr_id[] = $query->id;
                        unset($arr_create);
                    }
                }
            }

            //Если есть неизменные значения в таблице связей
            if (!empty($arr_static)) {
                $arr_id_db = $model::where(function ($q) use ($arr_static) {
                    foreach ($arr_static as $key2 => $val2) {
                        $q->where($key2, $val2);
                    }
                })->pluck('id')->toArray();
            } else {
                $arr_id_db = $model::all()->pluck('id')->toArray();
            }
            $arr_diff = array_diff($arr_id_db, $arr_id);
            if (!empty($arr_diff)) {
                $destroy_query = $model::destroy($arr_diff);
            }
        }
    }

    /**
     * Сохранение связи с изменением данных в ней / $increment_field - нужен для определения есть ли связь в бд, и нужно ли ее редактированть, после чего проверка на id, и удаление не соответствующих записей
     * @param $model
     * @param $array_value
     * @param null $arr_static
     * @param null $increment_field
     */
    public function SaveEditRelations_trait($model, $array_value, $arr_static = null, $increment_field = null)// Сохранение связи с изменением данных в ней / $increment_field - нужен для определения есть ли связь в бд, и нужно ли ее редактированть, после чего проверка на id, и удаление не соответствующих записей
    {
        if (empty($increment_field)) {
            $this->SaveChangeRelations_trait($model, $array_value, $arr_static);
        } else {
            $increment_field = (is_array($increment_field)) ? $increment_field : $array_value[$increment_field];
            if (!empty($array_value)) {
                if (strripos($model, "\\") === false) $model = "App\\" . $model;
                $model_exemp = new $model;
                $arr_id = [];
                foreach ($increment_field as $key => $id) {
                    if (!empty($id)) {
                        $record = $model::find($id);
                        if ($record) {
                            foreach ($array_value as $key1 => $val) {
                                if ($model_exemp->isFillable($key1)) {
                                    if (isset($model::$dateFields) && sizeof($model::$dateFields) > 0) {
                                        if (in_array($key1, $model::$dateFields)) $val[$key] = $this->date_format_val($val[$key], 'Y-m-d');
                                    }
                                    if ($record->$key1 != $val[$key] && $val[$key] != "") {
                                        $record->$key1 = $val[$key];
                                    } elseif ($val[$key] == "") {
                                        $record->$key1 = null;
                                    }
                                }
                            }
                            //Если в записи было изменено хоть 1 поле, сохраняем его
                            if ($record->isDirty()) {
                                $record->save();
                            }
                            $arr_id[] = $record->id;
                        }
                    } else {
                        foreach ($array_value as $key1 => $val1) {  //Перебираем массив со значениями
                            if ($model_exemp->isFillable($key1)) {
                                if (array_key_exists($key, $val1)) {    //Проверям существует ли ключь в массиве значений
                                    if (!empty($val1[$key])) {
                                        $arr_create[$key1] = $val1[$key];
                                    } else {
                                        $arr_create[$key1] = null;
                                    }
                                }
                            }
                        }
                        if (!empty($arr_create)) {  //Если есть новые записи создаем их!
                            if (!empty($arr_static)) $arr_create = array_merge($arr_static, $arr_create);
                            if (isset($model::$dateFields) && sizeof($model::$dateFields) > 0) {
                                $arr_create = $this->date_format_val($arr_create, "Y-m-d", $model::$dateFields);
                            }
                            $query = $model::firstOrCreate($arr_create);
                            $arr_id[] = $query->id;
                        }

                    }
                }
                //Если есть неизменные значения в таблице связей
                if (!empty($arr_static)) {
                    $arr_id_db = $model::where(function ($q) use ($arr_static) {
                        foreach ($arr_static as $key2 => $val2) {
                            $q->where($key2, $val2);
                        }
                    })->pluck('id')->toArray();
                } else {
                    $arr_id_db = $model::all()->pluck('id')->toArray();
                }
                $arr_diff = array_diff($arr_id_db, $arr_id);
                if (!empty($arr_diff)) {
                    $destroy_query = $model::destroy($arr_diff);
                }
            }
        }
    }
    

    /**
     * @param $id
     * @param null $model
     * @return mixed
     */
    public function Destroy_trait($id, $model = null)
    {
        if (isset($id)) {
            if (strripos($model, "\\") === false) {
                $model = "App\\" . $model;
            }
            $destroy = $model::destroy($id);
            return $destroy;
        }
    }

    /**
     *  REMOVE FILES
     * @param $dir
     */
    public function removeDirectory($dir)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }
//    public function LogsSave_trait($action = null, $record_id = null, $field_for_logs = null, $new_record_json = null, $old_record_json = null, $table_name = null)
//    {
//        if (!empty($action)) {
//            $MODEL = "App\\logs_model";
//            $array_val_stat = [
//                'table_name_real' => (isset($this->configCont['table_name_real'])) ? $this->configCont['table_name_real'] : null,
//                'table_name' => (isset($this->configCont['table_name_ru'])) ? $this->configCont['table_name_ru'] : $table_name,
//                'cont_name' => (isset($this->configCont['cont_name'])) ? $this->configCont['cont_name'] : null,
//            ];
//            $array_val = [
//                'action' => $action,
//                'record_id' => $record_id,
//                'user_id' => Auth::user()->id,
//                'user_name' => Auth::user()->login,
//                'old_record_json' => $old_record_json,
//                'new_record_json' => $new_record_json,
//                'field_for_logs' => $field_for_logs,
//            ];
//            $array_val = array_merge($array_val, $array_val_stat);
//
//            foreach ($array_val as $key => $val) {
//                if (empty($val)) unset($array_val[$key]);
//            }
//            $logs = $MODEL::create($array_val);
//            return $logs;
//        }
//    }



    public function LogsSave_trait($action_id = null, $new_record = null, $old_record = null, $array_value = null)
    {
        if (!empty($action_id)) {
            $MODEL = "App\\logs_model";
            $array_val = [
                'action_id' => $action_id,
                'action' => (!empty($action_id) && array_key_exists($action_id, $this->action)) ? $this->action[$action_id] : null,
                'user_id' => Auth::user()->id,
                'user_login' => Auth::user()->login,
                'name' => Auth::user()->name,
                'surname' => Auth::user()->surname,
                'patronymic' => Auth::user()->patronymic,
                'table_name_real' => (isset($this->configCont['table_name_real'])) ? $this->configCont['table_name_real'] : null,
                'table_name' => (isset($this->configCont['table_name_ru'])) ? $this->configCont['table_name_ru'] : null,
                'record_id' => !empty($new_record) ? $new_record['id'] : null,
                'new_record_json' => !empty($new_record) ? json_encode($new_record, JSON_UNESCAPED_UNICODE) : null,
                'old_record_json' => !empty($old_record) ? json_encode($old_record, JSON_UNESCAPED_UNICODE) : null,
                'field_for_logs_json' => !empty($field_for_logs_json) ? json_encode($field_for_logs_json, JSON_UNESCAPED_UNICODE) : null,
            ];
            if (!empty($array_value)) {
                $array_val = array_merge($array_val, $array_value);
            }
            foreach ($array_val as $key => $val) {
                if (empty($val)) unset($array_val[$key]);
            }
            $logs = $MODEL::create($array_val);
            return $logs;
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /***************************************************************Работа с JSON*********************************************************************************************************/

    /**
     * Базовая функция для сохранения в БД с JSON
     * @param null $instance_model
     * @param array $array_input
     * @param array $add_array
     * @param string $jsonable
     * @return array
     */

    public function defaultByJson($instance_model = null, $array_input = [], $add_array = [], $arr_rules = [], $jsonable = 'items')
    {
        if (!empty($array_input) && !empty($instance_model)) {
            $default_prefix = '';
            $array_fillable = $instance_model->getFillable();
            $prefix_model = (method_exists($instance_model, 'getPrefixModel')) ? $instance_model->getPrefixModel() : $default_prefix;
            $jsonable = (property_exists($instance_model, 'jsonable')) ? $instance_model->jsonable : $jsonable;
            $record = [];
            $array_fields_by_model = $this->splitArrayByPrefix($array_input, $prefix_model);
            if (empty($array_fields_by_model['value'])) {
                $array_save = $add_array;
                foreach ($array_save as $keySave => $valSave) {
                    if (!in_array($keySave, $array_fillable)) {
                        unset($array_save[$keySave]);
                    }
                }
                $record[] = $array_save;
            } else {
                foreach ($array_fields_by_model['value'] as $value) {
                    if (!empty($value)) {
                        $rules = $this->splitArrayRules($value, $arr_rules,true);
                        $array_save = array_merge([
                            $jsonable => $this->json_encode_trait($value),
                        ], $add_array, $rules);

                        foreach ($array_save as $keySave => $valSave) {
                            if (!in_array($keySave, $array_fillable)) {
                                unset($array_save[$keySave]);
                            }
                        }
                        $record[] = $array_save;
                    }
                }
            }
            return $record;
        }
    }

    /*
    * Сохранение записи в json поле по префиксу
    * @param null $model
    * @param array $array_input
    * @param array $add_array
    * @param string $jsonable
    * @return array
    */

    public function saveByJson($model = null, $array_input = [], $add_array = [], $arr_rules = [], $jsonable = 'items')
    {
        if (is_string($model)) {
            $instance_model = new $model;
        } elseif ($model instanceof Model) {
            $instance_model = $model;
        }
        $array_save = $this->defaultByJson($instance_model, $array_input, $add_array, $arr_rules, $jsonable);
        return $instance_model::firstOrNew($array_save[0]);
    }

    public function createByJson($model = null, $array_input = [], $add_array = [], $arr_rules = [], $jsonable = 'items')
    {
        if (is_string($model)) {
            $instance_model = new $model;
        } elseif ($model instanceof Model) {
            $instance_model = $model;
        }

        $array_save = $this->defaultByJson($instance_model, $array_input, $add_array, $arr_rules, $jsonable);
        $save_record = [];
        foreach ($array_save as $item) {
            $save_record[] = $instance_model::create($item);
        }
        if (count($save_record) == 1) {
            return $save_record[0];
        } else {
            return $save_record;
        }
    }

    /**
     *  Обычное сохранение от экземпляра отношения
     * @param $relation
     * @param $data
     * @param array $add_array
     * @param string $jsonable
     * @return array|bool|mixed
     */
    public function createByJsonFromInstance($relation, $data, $add_array = [], $arr_rules = [], $jsonable = 'items')
    {
        $responce = false;
        if (!empty($relation)) {
            $related_model = $relation->getRelated();
            $array_save = $this->defaultByJson($related_model, $data, $add_array, $arr_rules, $jsonable);
            $save_record = [];
            foreach ($array_save as $record) {
                $save_record[] = $relation->create($record);
            }

            if (count($save_record) == 1) {
                $responce = $save_record[0];
            } else {
                $responce = $save_record;
            }
        }
        return $responce;
    }

    public function saveSimpleRecordJson($model = null, $json_array = [], $add_array = [], $jsonable = 'items')
    {
        if (!empty($model)) {

            if (is_string($model)) {
                $instance_model = new $model;
            } elseif ($model instanceof Model) {
                $instance_model = $model;
            }

            $array_save = array_merge([
                $jsonable => $this->json_encode_trait($json_array),
            ], $add_array);

            return $instance_model->fill($array_save);
        } else {
        }
        return null;
    }

    /**
     * Обновление записей в json
     * @param null $record
     * @param null $input
     * @param null $add_array
     * @return bool
     */

    public function updateJsonRecord_trait($record = null, $input = [], $add_array = [], $with_input_value = false, $prefix = null, $jsonable = 'items')
    {
        if (!empty($input) && !empty($record) && $record instanceof Model) {
            $prefix = (empty($prefix)) ? $record->getPrefixModel() : $prefix;
            $field = (property_exists($record, 'jsonable')) ? $record->jsonable : $jsonable;

            $array_fields_by_model = $this->splitArrayByPrefix($input, $prefix)['value'][0];
            $array_save = [
                $field => $this->json_encode_trait($array_fields_by_model),
            ];
            $value_from_input = [];
            if ($with_input_value) {
                foreach ($input as $keyInp => $valueInp) {
                    if ($record->isFillable($keyInp)) {
                        $value_from_input[$keyInp] = $valueInp;
                    }
                }
            }
            $array_save = array_merge($array_save, $add_array, $value_from_input);
            return $this->updateRecord_trait($record, $array_save);
        } else {
            return false;
        }
    }


    /**
     * @param null $record_eloquent
     * @param null $array_val
     * @return bool|null
     */
    public function updateRecord_trait($record_eloquent = null, $array_val = null, $rules = [])
    {
        if (!empty($record_eloquent) && !empty($array_val)) {
            $array_val = $this->convertArrayRules($array_val, $rules, true);
            $record_eloquent->fill($array_val);
            $record_eloquent->save();
            return $record_eloquent;
        } else {
            return null;
        }
    }

    /**
     * @param bool $value
     * @return string
     */
    public function json_encode_trait($value = false)
    {
        if ($value) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Разбивает табличные реквесты на записи
     * @param null $array_value
     * @param null $prefix
     * @return array|bool
     */
    public static function splitArrayByPrefix($array_value = null, $prefix = null, $key_id = null)
    {
        if (!empty($array_value) && !empty($prefix)) {
            $records_array = [];
            $record_id = [];
            foreach ($array_value as $inputKey => $inputValue) {
                if (empty($key_id)) {
                    $is_id_field = preg_match('/.+_id$/', $inputKey);
                } else {
                    $is_id_field = ($key_id == $inputKey) ? true : false;
                }
                if (preg_match("/^$prefix.*/u",$inputKey) && !$is_id_field) {
                    if (is_array($inputValue)) {
                        foreach ($inputValue as $keyInInput => $valInInput) {
                            if (array_key_exists($keyInInput, $records_array)) {
                                $records_array[$keyInInput][$inputKey] = $valInInput;
                            } else {
                                $records_array[$keyInInput] = [$inputKey => $valInInput];
                            }
                        }
                    } else {
                        if (array_key_exists(0, $records_array)) {
                            $records_array[0][$inputKey] = $inputValue;
                        } else {
                            $records_array[0] = [$inputKey => $inputValue];
                        }
                    }
                } elseif (stristr($inputKey, $prefix) && $is_id_field) {
                    if (is_array($inputValue)) {
                        $record_id = $inputValue;
                    } else {
                        $record_id[0] = $inputValue;
                    }
                }
            }
            return ['value' => $records_array, 'id' => $record_id];
        } else {
            return false;
        }
    }


    /**
     * @param null $model
     * @param null $array_value
     * @param array $arr_static
     * @return bool
     */
    public function saveRelationByJson($model = null, $array_value = null, $arr_for_relation = [], $add_array = [],$rules_array = [])
    {
        if (!empty($model) && is_string($model) && !empty($array_value)) {
            if($arr_for_relation == null) $arr_for_relation = [];
            if($add_array == null) $add_array = [];
            if($rules_array == null) $rules_array = [];
            $default_prefix = '';
            $default_jsonable = 'items';
            $instance_model = new $model;
            $prefix_model = (method_exists($instance_model, 'getPrefixModel')) ? $instance_model->getPrefixModel() : $default_prefix;
            $jsonable = (property_exists($instance_model, 'jsonable')) ? $instance_model->jsonable : $default_jsonable;
            $arr_id = [];
            $records_array = $this->splitArrayByPrefix($array_value, $prefix_model);

//            if (true) {
                foreach ($records_array['value'] as $recKey => $recVal) {
                    $rules = $this->splitArrayRules($recVal, $rules_array);
                    $array_to_save = array_merge([
                        $jsonable => $this->json_encode_trait(array_map(function ($val) {
                            if ($val == null) {
                                return '';
                            } else {
                                return $val;
                            }
                        }, $recVal)),
                    ], $arr_for_relation, $add_array,$rules);
                    if (!empty($array_to_save)) {
                        $clone_instance_model = clone $instance_model;
                        $save_record = $clone_instance_model->firstOrNew(['id' => $records_array['id'][$recKey]]);
                        $save_record->fill($array_to_save);
                        $save_record->save();
                        $arr_id[] = $save_record->id;
                    }
                }
//            }
//            else {
//                foreach ($records_array['id'] as $incKey => $incVal) {
//                    if ($incVal == '') $incVal = null;
//                    $clone_instance_model = clone $instance_model;
//                    $array_to_save = array_merge([
//                        $jsonable => $this->json_encode_trait(array_map(function ($val) {
//                            if ($val == null) {
//                                return '';
//                            } else {
//                                return $val;
//                            }
//                        }, $records_array['value'][$incKey])),
//                    ], $arr_for_relation);
//                    if (empty($incVal)) $array_to_save = array_merge($array_to_save, $add_array);
//                    $save_record = $clone_instance_model->firstOrNew(['id' => $incVal]);
//                    $save_record->fill($array_to_save);
//                    $save_record->save();
//                    $arr_id[] = $save_record->id;
//                }
//            }

            //Если есть неизменные значения в таблице связей
            if (!empty($arr_for_relation)) {
                $arr_id_db = $instance_model::where(function ($q) use ($arr_for_relation) {
                    foreach ($arr_for_relation as $key2 => $val2) {
                        $q->where($key2, $val2);
                    }
                })->pluck('id')->toArray();
            } else {
                $arr_id_db = $instance_model::all()->pluck('id')->toArray();
            }

            $arr_destroy = array_diff($arr_id_db, $arr_id);

            $arr_save = array_diff($arr_id, $arr_destroy);
            if (!empty($arr_destroy)) {
                $destroy_query = $instance_model::destroy($arr_destroy);
            }
            $last_record = $instance_model::whereIn('id', $arr_save)->get();
            return $last_record;
        }
        return null;
    }


    /**
     * @param array $value
     * @param array $rules
     * @param bool $delete
     * @return array
     */
    public static function convertArrayRules($value = [], $rules = [], $delete = false)
    {
        if (!empty($value) && !empty($rules) && is_array($rules)) {
            foreach ($rules as $field => $key) {
                if (array_key_exists($key, $value)) {
                    $value[$field] = $value[$key];
                    if ($delete) unset($value[$key]);
                }
            }
        }
        return $value;
    }

    /**
     * @param $relation
     * @param $data
     * @param array $add_array
     * @param array $add_json
     * @param array $rules_array
     * @param string $prefix
     * @param string $jsonable
     * @return bool
     */
    public function saveRelationByJsonForInstance($relation, $data, $add_array = [], $add_json = [], $rules_array = [], $prefix = '', $jsonable = 'items')
    {

        $response = false;
        if (!empty($relation)) {
            $id_arr = [];
            $related_model = $relation->getRelated();
            $prefix = (method_exists($related_model, 'getPrefixModel')) ? $related_model->getPrefixModel() : $prefix;
            $jsonable = (property_exists($related_model, 'jsonable')) ? $related_model->jsonable : $jsonable;
            $data_split = $this->splitArrayByPrefix($data, $prefix);
            if (!empty($data_split['id'])) {
                foreach ($data_split['value'] as $key => $field) {
                    $rel = clone $relation;
                    $rules = $this->splitArrayRules($field, $rules_array);
                    $id = $data_split['id'][$key];
                    $record = array_merge($field, $add_json);
                    $data_save = array_merge([
                        $jsonable => $this->json_encode_trait($record),
                    ], $add_array, $rules);
                    $instance = $rel->firstOrNew(['id' => $id]);
                    $instance->fill($data_save);
                    $instance->save();
                    $id_arr[] = $instance->id;
                }
                $diff_id = $relation->get()->pluck('id')->diff($id_arr)->toArray();
                if (!empty($diff_id)) {
                    $destroy_count = $relation->getRelated()->destroy($diff_id);
                }
                $response = $relation->get();
            }
            if(empty($data_split['value'])){
                $relation->delete();
            }
        }
        return $response;
    }


    /**
     * @param null $model
     * @param array $array_val
     * @param array $array_not_like
     * @param string $json_field
     * @return null
     */
    public function searchInJson_trait($model = null, $array_val = [], $array_not_like = [], $json_field = 'items')
    {
        $response = null;
        if (!empty($array_val)) {
            if (!is_object($model)) {
                if (strripos($model, "\\") === false) $model = "App\\" . $model;
                $model = new $model;
            }
            $void_val = false;
            foreach ($array_val as $item) {
                if (!empty($item) && $void_val === false) $void_val = true;
            }
            if ($void_val) {
                $query = $model::where(function ($q) use ($array_val, $array_not_like, $model, $json_field) {
                    $q->whereRaw('1=1');
                    foreach ($array_val as $key => $val) {
                        if (!empty($val)) {
                            $key_sr = $json_field . '->' . $key;
                            if (!empty($array_not_like) && in_array($key, $array_not_like)) {
                                $q->where($key_sr, $val);
                            } else {
                                $q->where($key_sr, 'ILIKE', "%$val%");
                            }
                        }
                    };
                });
                $response = $query;
            }
        }
        return $response;
    }



    public function array_merge_custom()
    {
        $arg = func_get_args();
        $response = [];
        if (!empty($arg)) {
            foreach ($arg as $param) {
                if (!empty($param) && is_array($param)) {
                    foreach ($param as $key => $val) {
                        $response[$key] = $val;
                    }
                }
            }
        }
        return $response;
    }

    public function returnRepeatValueInArray($array = [])
    {
        if (!empty($array)) {
            $array = array_keys(array_filter(array_count_values($array), function ($v) {
                return $v > 1;
            }));
        }
        return $array;
    }

    /**
     * Convert empty value to string
     * @param $array
     * @return array
     */
    public function nullToStr($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                if ($val == null) {
                    $array[$key] = '';
                }
            }
        }
        return $array;
    }

    /**
     * функция для проверки на наличее хоть одного value в ассоциативном массиве
     */
    public function checkEmptyValArr($array)
    {
        $response = false;
        array_walk($array,function($val,$key) use (&$response){
            if(!empty($val)) $response = true;
        });
        return $response;
    }

    /**
     * Функция для обновления json поля
     */

    public function updateJSONField($value = null,$key = '', $val = '')
    {
        if(!empty($value) && is_string($value)){
            $value = preg_replace("/(?<=\"$key\": \")[^\"]*/",$val,$value);
        }
        return $value;
    }


    /**
     * get data from array and json
     * @param $array
     * @param $key
     * @return mixed|null
     */
    public static function getArrayData($array, $key)
    {
        $keys_arr = explode('.', $key);
        foreach ($keys_arr as $key=>$val){
            if(is_array($array) && array_key_exists($val,$array)){
                $array = $array[$val];
            }elseif(is_string($array) && json_decode($array) && isset(json_decode($array, true)[$val])){
                $array = json_decode($array, true)[$val];
            }else{
                return null;
            }
        }
        return (empty($array))? null : $array;
    }

    public function replaceStringWithArray(&$string, $array)
    {
        foreach ($array as $key=>$val) {
            $pattern = "($key)";
            $replacement = (empty($val))? '' : " $val ";
            $string = preg_replace($pattern, $replacement, $string);
        }
        return $string;
    }

}//END