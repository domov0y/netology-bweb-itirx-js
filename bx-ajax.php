<?
//подключаем пролог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

//устанавливаем заголовок страницы
$APPLICATION->SetTitle("AJAX");

//подключение библиотеки ajax
   CJSCore::Init(array('ajax'));
   $sidAjax = 'testAjax';

/*
здесь практика вызывающая много матерных слов.
если есть ajax_form = testAjax то послать в /dev/null 
те пол секунды которые ушли на генерацию шапки из тяжелого шаблона
и выдать вместо страницы json. 

в данном случае ессмысленный и беспощадный
array(
            'RESULT' => 'HELLO',
            'ERROR' => ''
   )
*/

if(isset($_REQUEST['ajax_form']) && $_REQUEST['ajax_form'] == $sidAjax){
//отбросить все что выводилось до этого
   $GLOBALS['APPLICATION']->RestartBuffer();\
//а тут хватило бы json_encode   
   echo CUtil::PhpToJSObject(array(
            'RESULT' => 'HELLO',
            'ERROR' => ''
   ));
   die();
   /*прекратить выполнение скрипта*/
}

?>
<div class="group">
   <div id="block"></div >
   <div id="process">wait ... </div >
</div>

<script>
   //какая то отладка. 
   window.BXDEBUG = true;

function DEMOLoad(){
   //скрыть div#block, показать div#process
   BX.hide(BX("block"));
   BX.show(BX("process"));

  //загрузить json через ajax. по завершении выполнить функцию DEMOResponse
   BX.ajax.loadJSON(
      '<?=$APPLICATION->GetCurPage()?>?ajax_form=<?=$sidAjax?>',
      DEMOResponse
   );
}
function DEMOResponse (data){
   //вывести  массив в консоль
   BX.debug('AJAX-DEMOResponse ', data);

   //упоролись и заменили document.getElementById("block") на BX("block"). возможно правильно.
   BX("block").innerHTML = data.RESULT;

   //показать div#block скрыть div#process
   BX.show(BX("block"));
   BX.hide(BX("process"));

   /*здесь код отработает вхолостую. 
   если бы в ready не закоментировали addCustomEvent то было бы имитировано событие DEMOUpdate и как следствие выполнена функция 
   */
   BX.onCustomEvent(
      BX(BX("block")),
      'DEMOUpdate'
   );
}

//когда страница загужена  выполнить функцию в скобках
BX.ready(function(){
   /*
   BX.addCustomEvent(BX("block"), 'DEMOUpdate', function(){
      window.location.href = window.location.href;
   });
   */
  //скрыть  блоки  #block #progress
   BX.hide(BX("block"));
   BX.hide(BX("process"));
   
   // очень странный способ сказать $('.css_class').click(DEMOLoad);

    BX.bindDelegate(
      document.body, 'click', {className: 'css_ajax' },
      function(e){
         if(!e)
            e = window.event;
         //выполнить DEMOLoad
         DEMOLoad();
         /*отменить выполнение всяких стандартных действий. 
         имеет смысл если вызывает ссылка или отправка формы*/
         return BX.PreventDefault(e);
      }
   );
   
});

</script>
<div class="css_ajax">click Me</div>
<?
//подключаем эпилог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
