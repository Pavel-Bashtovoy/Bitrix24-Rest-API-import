<?php
function executeHook($queryUrl,$queryData) 
{
    // обращаемся к Битрикс24 при помощи функции curl_exec
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_POST => 1,
      CURLOPT_HEADER => 0,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $queryUrl,
      CURLOPT_POSTFIELDS => $queryData,
    ));
    $result = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($result, true);
    if (array_key_exists('error', $result))echo "Ошибка при сохранении списка: ".$result['error_description']."<br/>";
}
//добавляем список на портал
function lists_add($Webhook_URL,$list_name,$block_code)
{
    // формируем URL в переменной $queryUrl
    $queryUrl = "$Webhook_URL/lists.add.json";

    // формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
        'IBLOCK_TYPE_ID'=>'lists',
        'IBLOCK_CODE'=>"$block_code",
    'fields' => array(
        'NAME' =>"$list_name",
        'DESCRIPTION' => 'Здесь будут данные',
        'SORT'=> '500',
        'BIZPROC'=> 'Y'

    )
    ));
    executeHook($queryUrl,$queryData);
}
//добавляем элементы списков-справочников пакетом запросов
function elements_add($Webhook_URL,$block_code,$csv_import_file)
{
    $queryUrl = "$Webhook_URL/batch.json";
    $batch = array();
    $res = '';
    $file =$csv_import_file;
    //открываем файл с CSV-данными
    $fh = fopen($file, "r");

    // делаем пропуск первой строки, смещая указатель на одну строку
    fgetcsv($fh, 0, ';');
    $i=0;
    //читаем построчно содержимое CSV-файла
    while (($row = fgetcsv($fh, 0, ';')) !== false) 
    {
        $i=$i+1;
        //вывел на экран то что считал из файла
        echo '<br>'.$i.''.$row[0].'</br>';
//заполнение пакета
        $batch['cmd_'.$i] =
            'lists.element.add?'.http_build_query(
                array(
                    'IBLOCK_TYPE_ID'=>'lists',
                    'IBLOCK_CODE'=>"$block_code",
                    'ELEMENT_CODE'=>"$i",
                'fields' => array(
                    'NAME' => "$row[0]"
                )
                )
            );

        if ($i % 50 == 0) 
         {
             $res .= '>>> '.print_r(executeHook($queryUrl,http_build_query(array('cmd' => $batch))), true);
             $batch = array();
         }
    }
   if (count($batch) > 0) $res .= '>>> '.print_r(executeHook($queryUrl,http_build_query(array('cmd' => $batch))), true);
    return 'Список заполнен:<br/><br/>'.$res;
}
//---------------------------------------------------------------
//создание полей в списках
//добавление поля-числа
function number_field_add($Webhook_URL,$block_code,$field_name,$field_code)
{
    // формируем URL в переменной $queryUrl
    $queryUrl = "$Webhook_URL/lists.field.add.json";

    // формируем параметры для создания 
    $queryData = http_build_query(array(
        'IBLOCK_TYPE_ID'=>'lists',
        'IBLOCK_CODE'=>"$block_code",
        'fields' => array(
            'NAME' => "$field_name",
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'TYPE'=>"N",
            'CODE'=>"$field_code",
            'SETTINGS'=>array(
                'SHOW_ADD_FORM'=>'Y',
                'SHOW_EDIT_FORM'=>'Y',
                'ADD_READ_ONLY_FIELD'=>'N',
                'EDIT_READ_ONLY_FIELD'=>'N',
                'SHOW_FIELD_PREVIEW'=>'N'
            )
        )
    ));
    executeHook($queryUrl,$queryData); 
}
//добавление поля-строки
function string_field_add($Webhook_URL,$block_code,$field_name,$field_code)
{
    // формируем URL в переменной $queryUrl
    $queryUrl = "$Webhook_URL/lists.field.add.json";

    // формируем параметры для создания 
    $queryData = http_build_query(array(
        'IBLOCK_TYPE_ID'=>'lists',
        'IBLOCK_CODE'=>"$block_code",
        'fields' => array(
            'NAME' => "$field_name",
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'TYPE'=>"S",
            'CODE'=>"$field_code",
            'SETTINGS'=>array(
                'SHOW_ADD_FORM'=>'Y',
                'SHOW_EDIT_FORM'=>'Y',
                'ADD_READ_ONLY_FIELD'=>'N',
                'EDIT_READ_ONLY_FIELD'=>'N',
                'SHOW_FIELD_PREVIEW'=>'N'
            )
        )
    ));
    executeHook($queryUrl,$queryData); 
}
//добавление поля-списка уровня образования
function level_of_education_list_field_add($Webhook_URL,$block_code)
{
    // формируем URL в переменной $queryUrl
    $queryUrl = "$Webhook_URL/lists.field.add.json";

    // формируем параметры для создания 
    $queryData = http_build_query(array(
        'IBLOCK_TYPE_ID'=>'lists',
        'IBLOCK_CODE'=>"$block_code",
        'fields' => array(
            'NAME' => "Маг/бак",
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'TYPE'=>"L",
            'CODE'=>"Level_education",
            'LIST_TEXT_VALUES'=> "магистр\n бакалавр\n",
            'SETTINGS'=>array(
                'SHOW_ADD_FORM'=>'Y',
                'SHOW_EDIT_FORM'=>'Y',
                'ADD_READ_ONLY_FIELD'=>'N',
                'EDIT_READ_ONLY_FIELD'=>'N',
                'SHOW_FIELD_PREVIEW'=>'N'
            )
        )
    ));
    executeHook($queryUrl,$queryData); 
}

//добавление поля-отзыва воспоминания
function pozitive_negative_field_add($Webhook_URL,$block_code)
{
    // формируем URL в переменной $queryUrl
    $queryUrl = "$Webhook_URL/lists.field.add.json";

    // формируем параметры для создания 
    $queryData = http_build_query(array(
        'IBLOCK_TYPE_ID'=>'lists',
        'IBLOCK_CODE'=>"$block_code",
        'fields' => array(
            'NAME' => "Позитивное или нет",
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'TYPE'=>"L",
            'CODE'=>"pozitiv_negativ",
            'LIST_TEXT_VALUES'=> "Позитивное\n Негативное\n",
            'SETTINGS'=>array(
                'SHOW_ADD_FORM'=>'Y',
                'SHOW_EDIT_FORM'=>'Y',
                'ADD_READ_ONLY_FIELD'=>'N',
                'EDIT_READ_ONLY_FIELD'=>'N',
                'SHOW_FIELD_PREVIEW'=>'N'
            )
        )
    ));
    executeHook($queryUrl,$queryData); 
}

//добавление поля-списка оценок
function rating_list_field_add($Webhook_URL,$block_code)
{
    // формируем URL в переменной $queryUrl
    $queryUrl = "$Webhook_URL/lists.field.add.json";

    // формируем параметры для создания 
    $queryData = http_build_query(array(
        'IBLOCK_TYPE_ID'=>'lists',
        'IBLOCK_CODE'=>"$block_code",
        'fields' => array(
            'NAME' => "Общая оценка",
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'TYPE'=>"L",
            'CODE'=>"Raiting",
            'LIST_TEXT_VALUES'=> "5\n 4\n 3\n 2\n",
            'SETTINGS'=>array(
                'SHOW_ADD_FORM'=>'Y',
                'SHOW_EDIT_FORM'=>'Y',
                'ADD_READ_ONLY_FIELD'=>'N',
                'EDIT_READ_ONLY_FIELD'=>'N',
                'SHOW_FIELD_PREVIEW'=>'N'
            )
        )
    ));
    executeHook($queryUrl,$queryData); 
}

//Добавление поля подключения к CRM контактам
function CRM_contact_link_field_add($Webhook_URL,$block_code,$field_name,$field_code)
{
   // формируем URL в переменной $queryUrl
   $queryUrl = "$Webhook_URL/lists.field.add.json";

   // формируем параметры для создания 
   $queryData = http_build_query(array(
       'IBLOCK_TYPE_ID'=>'lists',
       'IBLOCK_CODE'=>"$block_code",
        'fields' => array(
            'NAME' => "$field_name",
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'TYPE'=>"S:ECrm",
            'USER_TYPE_SETTINGS'=>array(
                "CONTACT"=>"Y",
                "VISIBLE"=>"Y"),
            'CODE'=>"$field_code",
            'SETTINGS'=>array(
                'SHOW_ADD_FORM'=>'Y',
                'SHOW_EDIT_FORM'=>'Y',
                'ADD_READ_ONLY_FIELD'=>'N',
                'EDIT_READ_ONLY_FIELD'=>'N',
                'SHOW_FIELD_PREVIEW'=>'N'
            )
        )
   ));
   executeHook($queryUrl,$queryData); 
}

//Добавление поля подключения к CRM компании
function CRM_сompanies_link_field_add($Webhook_URL,$block_code,$field_name,$field_code)
{
   // формируем URL в переменной $queryUrl
   $queryUrl = "$Webhook_URL/lists.field.add.json";

   // формируем параметры для создания 
   $queryData = http_build_query(array(
       'IBLOCK_TYPE_ID'=>'lists',
       'IBLOCK_CODE'=>"$block_code",
        'fields' => array(
            'NAME' => "$field_name",
            'IS_REQUIRED' => 'Y',
            'MULTIPLE' => 'N',
            'TYPE'=>"S:ECrm",
            'USER_TYPE_SETTINGS'=>array(
                "COMPANY"=>"Y",
                "VISIBLE"=>"Y"),
            'CODE'=>"$field_code",
            'SETTINGS'=>array(
                'SHOW_ADD_FORM'=>'Y',
                'SHOW_EDIT_FORM'=>'Y',
                'ADD_READ_ONLY_FIELD'=>'N',
                'EDIT_READ_ONLY_FIELD'=>'N',
                'SHOW_FIELD_PREVIEW'=>'N'
            )
        )
   ));
   executeHook($queryUrl,$queryData); 
}
//привязка к элементам
function link_to_element_field_add($Webhook_URL,$block_code,$field_name,$link_id)
{
   // формируем URL в переменной $queryUrl
   $queryUrl = "$Webhook_URL/lists.field.add.json";

   // формируем параметры для создания 
   $queryData = http_build_query(array(
       'IBLOCK_TYPE_ID'=>'lists',
       'IBLOCK_CODE'=>"$block_code",
       'fields' => array(
           'NAME' => "$field_name",
           'IS_REQUIRED' => 'N',
           'MULTIPLE' => 'N',
           'TYPE'=>"E",
           //обратить внимание на айдишку
           'LINK_IBLOCK_ID'=>"$link_id",
           'CODE'=>"Kompetencii",
           'SETTINGS'=>array(
               'SHOW_ADD_FORM'=>'Y',
               'SHOW_EDIT_FORM'=>'Y',
           )
       )
   ));
   executeHook($queryUrl,$queryData); 
}
//привязка к элементам в виде списка 
function link_to_element_of_list_field_add($Webhook_URL,$block_code,$field_name,$link_id)
{
   // формируем URL в переменной $queryUrl
   $queryUrl = "$Webhook_URL/lists.field.add.json";

   // формируем параметры для создания 
   $queryData = http_build_query(array(
       'IBLOCK_TYPE_ID'=>'lists',
       'IBLOCK_CODE'=>"$block_code",
       'fields' => array(
           'NAME' => "$field_name",
           'IS_REQUIRED' => 'Y',
           'MULTIPLE' => 'N',
           'TYPE'=>"E:EList",
           'USER_TYPE_SETTINGS'=>array(
               "group"=>"Y",
            "multiple"=>"Y"),
           //обратить внимание на айдишку
           'LINK_IBLOCK_ID'=>"$link_id",
           'CODE'=>"Kompetencii",
           'SETTINGS'=>array(
               'SHOW_ADD_FORM'=>'Y',
               'SHOW_EDIT_FORM'=>'Y',
           )
       )
   ));
   executeHook($queryUrl,$queryData); 
}
//получаем айди списка по код блоку

function GET_list($Webhook_URL,$block_code)
{
   // формируем URL в переменной $queryUrl
   $queryUrl = "$Webhook_URL/lists.get.json";

   // формируем параметры для создания 
   $queryData = http_build_query(array(
       'IBLOCK_TYPE_ID'=>'lists',
       'IBLOCK_CODE'=>"$block_code"
   ));
     // обращаемся к Битрикс24 при помощи функции curl_exec
     $curl = curl_init();
     curl_setopt_array($curl, array(
       CURLOPT_SSL_VERIFYPEER => 0,
       CURLOPT_POST => 1,
       CURLOPT_HEADER => 0,
       CURLOPT_RETURNTRANSFER => 1,
       CURLOPT_URL => $queryUrl,
       CURLOPT_POSTFIELDS => $queryData,
     ));
     $result = curl_exec($curl);
     curl_close($curl);
     $result = json_decode($result, 1);
     if (array_key_exists('error', $result))
     {
      echo "Ошибка при получении ID: ".$result['error_description']."<br/>";
     
     }
     else
     {
         $list_id=$result['result'][0]['ID'];
         return $list_id;
     }
}

///------------------------------------------------------------
//"https://ngtufpmi2.bitrix24.ru/rest/1/672yhgskac8uy8rd"

$Webhook_URL=$_REQUEST['Webhook'];

$step = intval($_REQUEST['step']);
$result = '';

switch ($step) {
    case 1:
		//список 1 должности 
		$list_name="Должности";
		$block_code="1231";
		$csv_import_file="dolzhnosti.csv";
		lists_add($Webhook_URL,$list_name,$block_code);
		elements_add($Webhook_URL,$block_code,$csv_import_file);
        break;

    case 2:
		//список 2 технологии
	$list_name="Технологии";
	$block_code="1232";
	$csv_import_file="technologies.csv";
	lists_add($Webhook_URL,$list_name,$block_code);
	elements_add($Webhook_URL,$block_code,$csv_import_file);
        break;

    case 3:
	//список 3 дисциплины
	$list_name="Дисциплины";
	$block_code="1233";
	$csv_import_file="disciplines.csv";
	lists_add($Webhook_URL,$list_name,$block_code);
	elements_add($Webhook_URL,$block_code,$csv_import_file);
        break;
	case 4:
	//список 4 кафедры
	$list_name="Кафедры";
	$block_code="1234";
	$csv_import_file="cathedra.csv";
	lists_add($Webhook_URL,$list_name,$block_code);
	elements_add($Webhook_URL,$block_code,$csv_import_file);
        break;
	case 5:
	//список 5 компетенции
	$list_name="Компетенции";
	$block_code="1235";
	$csv_import_file="competencies.csv";
	lists_add($Webhook_URL,$list_name,$block_code);
	elements_add($Webhook_URL,$block_code,$csv_import_file);
        break;
	case 6:
	//список 6 Владение компетенциями 
	$list_name="Владение компетенциями";
	$block_code="1236";
	lists_add($Webhook_URL,$list_name,$block_code);
	//создаем поля для списка 6
	$field_name="Выпускник";
	$field_code="Vypusknik";
	CRM_contact_link_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Компетенции";
	$field_code="Kompetencii";
	$link_block_code="1235";
	$link_id=GET_list($Webhook_URL,$link_block_code);
	link_to_element_of_list_field_add($Webhook_URL,$block_code,$field_name,$link_id);
        break;
	case 7:
	//список 7 владение технологиями 
	$list_name="Владение технологиями";
	$block_code="1237";
	lists_add($Webhook_URL,$list_name,$block_code);
	//создаем поля для списка 7
	$field_name="Выпускник";
	$field_code="Vypusknik";
	CRM_contact_link_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Технологии";
	$field_code="Technology";
	$link_block_code="1232";
	$link_id=GET_list($Webhook_URL,$link_block_code);
	link_to_element_of_list_field_add($Webhook_URL,$block_code,$field_name,$link_id);
	$field_name="Организация";
	$field_code="Organizaciya";
	CRM_сompanies_link_field_add($Webhook_URL,$block_code,$field_name,$field_code);
        break;
	case 8:
	//список 8 воспоминания
	$list_name="Воспоминания";
	$block_code="1238";
	lists_add($Webhook_URL,$list_name,$block_code);
	//создаем поля для списка 8
	$field_name="Выпускник";
	$field_code="Vypusknik";
	CRM_contact_link_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Преподаватель";
	$field_code="Prepodavatel";
	CRM_contact_link_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	pozitive_negative_field_add($Webhook_URL,$block_code);
        break;
	case 9:
	//список 9 направления 
	$list_name="Направления";
	$block_code="1239";
	lists_add($Webhook_URL,$list_name,$block_code);
	level_of_education_list_field_add($Webhook_URL,$block_code);
	$field_name="Код направления";
	$field_code="Kod_napravlenya";
	string_field_add($Webhook_URL,$block_code,$field_name,$field_code);
        break;
	case 10:
	//список 10 нужные дисциплины 
	$list_name="Нужные дисциплины";
	$block_code="1240";
	lists_add($Webhook_URL,$list_name,$block_code);
	//создаем поля для списка 10
	$field_name="Выпускник";
	$field_code="Vypusknik";
	CRM_contact_link_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Дисциплина";
	$field_code="Disciplina";
	$link_block_code="1233";
	$link_id=GET_list($Webhook_URL,$link_block_code);
	link_to_element_field_add($Webhook_URL,$block_code,$field_name,$link_id);
        break;
	case 11:
	//список 11 Периоды обучения 
	$list_name="Периоды обучения";
	$block_code="1241";
	lists_add($Webhook_URL,$list_name,$block_code);
	//создаем поля для списка 11
	$field_name="Выпускник";
	$field_code="Vypusknik";
	CRM_contact_link_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Направление";
	$field_code="Napravlenie";
	$link_block_code="1239";
	$link_id=GET_list($Webhook_URL,$link_block_code);
	link_to_element_field_add($Webhook_URL,$block_code,$field_name,$link_id);
	$field_name="Телефон во время обучения";
	$field_code="Telephone";
	string_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Год поступления";
	$field_code="God_postupleniya";
	number_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Год окончания";
	$field_code="God_okonchaniya";
	number_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	rating_list_field_add($Webhook_URL,$block_code);
        break;
	case 12:
	//список 12 Периоды работы
	$list_name="Периоды работы";
	$block_code="1242";
	lists_add($Webhook_URL,$list_name,$block_code);
	//создаем поля для списка 12
	$field_name="Выпускник";
	$field_code="Vypusknik";
	CRM_contact_link_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Компания";
	$field_code="Kompany";
	CRM_сompanies_link_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Год начала";
	$field_code="God_nachala";
	number_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Год окончания";
	$field_code="God_okonchaniya_raboty";
	number_field_add($Webhook_URL,$block_code,$field_name,$field_code);
	$field_name="Должность";
	$field_code="Dolzhnost";
	$link_block_code="1231";
	$link_id=GET_list($Webhook_URL,$link_block_code);
	link_to_element_field_add($Webhook_URL,$block_code,$field_name,$link_id);
        break;
    }
?>
