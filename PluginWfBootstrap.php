<?php
/**
<p>
Render Bootstrap widgets. Bootstrap v3.3.5 (http://getbootstrap.com).
</p>
 
 
 */
class PluginWfBootstrap{
  
  /**
  <p>
  Using Bootstrap Alert box. Use one of success, info, warning or danger for your purpose. Modify by using data/rewrite values.
  </p>
  <p>
  Default yml.
  </p>
  #code-yml#
  #load:[app_dir]/plugin/[plugin]/data/alert.yml:load#
  #code#
  <p>
  Change alert-success to one of alert-info, alert-warning, alert-danger.
  </p>
  #code-yml#
  type: widget
  data:
    plugin: 'wf/bootstrap'
    method: alert
    data:
      rewrite:
        'innerHTML': Hello
        'attribute/class': 'alert alert-success'
  #code#
   */
  public static function widget_alert($data){
    // Get default thumbnail.
    $element = wfFilesystem::loadYml(dirname(__FILE__).'/data/alert.yml');
    // Rewrite thumbnail.
    $element = self::rewrite($element, $data);
    // Render element.
    wfDocument::renderElement(array($element));
  }
  
  /**
  <p>
  Listgroup.
  </p>
  #code-yml#
  type: widget
  data:
    plugin: 'wf/bootstrap'
    method: listgroup
    data:
      item:
        -
          href: '#'
          target: _blank  
          innerHTML: 'One'
        -
          href: '#'
          innerHTML: 'Two'
          active: true
          onclick: "console.log('Onclick on a listgroup item.');"
  #code#
   */
  public static function widget_listgroup($data){
    $div = wfDocument::createHtmlElement('div', null, array('class' => 'list-group'));
    $a = array();
    if(wfArray::get($data, 'data/item')){
      foreach (wfArray::get($data, 'data/item') as $key => $value) {
        $active_class = null;
        if(wfArray::get($value, 'active')){
          $active_class = ' active';
        }
        $attribute = array();
        $attribute['class'] = 'list-group-item'.$active_class;
        
        if(wfArray::get($value, 'href')){
          $attribute['href'] = wfArray::get($value, 'href');
          if(wfArray::get($value, 'target')){
            $attribute['target'] = wfArray::get($value, 'target');
          }
          if(wfArray::get($value, 'onclick')){
            $attribute['onclick'] = wfArray::get($value, 'onclick');
          }
          $a[] = wfDocument::createHtmlElement('a', wfArray::get($value, 'innerHTML'), $attribute);
        }else{
          $a[] = wfDocument::createHtmlElement('span', wfArray::get($value, 'innerHTML'), $attribute);
        }
        
      }
    }  else {
      $a[] = wfDocument::createHtmlElement('span', '', array('class' => 'list-group-item'));
    }
    $div['innerHTML'] = $a;
    wfDocument::renderElement(array($div));
  }

  /**
  <p>
  Creating a Bootstrap menu. 
  </p>
  <p>
  Data example:
  </p>
  #code-yml#
  #load:[app_dir]/plugin/[plugin]/data/widget_menu_data.yml:load#
  #code#
  <p>
  Default element:
  </p>
  #code-yml#
  #load:[app_dir]/plugin/[plugin]/data/menu.yml:load#
  #code#
   */
  public static function widget_menu($data){
    $yml = wfArray::get($data, 'data', 'Data param is not set.');
    if(!is_array($yml)){
      $yml = wfSettings::getSettingsFromYmlString($yml);
    }
    //wfHelp::yml_dump($data, true);
    
    
    
    
    
    if(!wfArray::isKey($data, 'id')){
      $data['id'] = wfCrypt::getUid();
    }
    if(wfArray::isKey($data, 'brand')){
      if(!wfArray::isKey($yml, 'brand/lable')){
        $yml = wfArray::set($yml, 'brand/lable', '#');
      }
      if(!wfArray::isKey($yml, 'brand/href')){
        $yml = wfArray::set($yml, 'brand/href', '#');
      }
    }
    
    
    // Default element.
    $element = wfFilesystem::loadYml(dirname(__FILE__).'/data/menu.yml');
    
    // Set Bootstrap fluit.
    if(wfArray::get($yml, 'settings/fluit')){
      $element = wfArray::set($element, 'menu/attribute/class', 'container-fluid');
    }
    
    // Handle brand.
    if(wfArray::isKey($yml, 'brand')){
      $element = wfArray::set($element, 'menu/innerHTML/nav/innerHTML/container/innerHTML/header/innerHTML/brand/innerHTML', wfArray::get($yml, 'brand/lable'));
      $element = wfArray::set($element, 'menu/innerHTML/nav/innerHTML/container/innerHTML/header/innerHTML/brand/attribute/href', wfArray::get($yml, 'brand/href'));
    }else{
      $element = wfArray::setUnset($element, 'menu/innerHTML/nav/innerHTML/container/innerHTML/header');
    }
    
    // Set id...
    $element = wfArray::set($element, 'menu/innerHTML/nav/innerHTML/container/innerHTML/header/innerHTML/button/attribute/data-target', '#'.$data['id']);
    $element = wfArray::set($element, 'menu/innerHTML/nav/innerHTML/container/innerHTML/content/attribute/id', $data['id']);

    
    
    // Navbars.
    $navbars = array();
    foreach ($yml['navbar'] as $key => $value) {
      $item = array();
      if(wfArray::isKey($value, 'item')){
        // Navbar.
        
        // Sorting.
        $value['item'] = wfArray::sortMultiple($value['item'], 'sortorder');
        
        foreach ($value['item'] as $key2 => $value2) {
          if(!wfArray::isKey($value2, 'href') || wfArray::isKey($value2, 'item')){
            $value2['href'] = '#';
          }

          if(wfArray::isKey($value2, 'item')){

            //First level dropdown.
            $a = wfDocument::createHtmlElement('a', wfArray::get($value2, 'lable').' <span class="caret"></span>', array('href' => wfArray::get($value2, 'href'), 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'role' => 'button', 'aria-haspopup' => 'true', 'aria-expanded' => 'false'  ));
            $item2 = array();
            foreach ($value2['item'] as $key3 => $value3) {

              if(wfArray::issetAndTrue($value3, 'separator')){
                $li2 = wfDocument::createHtmlElement('li', null, array('role' => 'separator', 'class' => 'divider'));
              }else{
                //Second level.
                $a2 = wfDocument::createHtmlElement('a', wfArray::get($value3, 'lable'), array('href' => wfArray::get($value3, 'href'), 'onclick' => wfArray::get($value3, 'onclick'), 'id' => wfArray::get($value3, 'id')));
                $li2 = wfDocument::createHtmlElement('li', array($a2), array('class' => self::getActive(wfArray::get($value3, 'href'))), wfArray::get($value3, 'settings'));
              }

              $item2[] = $li2;
            }
            $ul = wfDocument::createHtmlElement('ul', $item2, array('class' => 'dropdown-menu'));



            $li = wfDocument::createHtmlElement('li', array($a, $ul), array('class' => 'dropdown'));
            $item[] = $li;
          }else{

            //First level.

            $a = wfDocument::createHtmlElement('a', wfArray::get($value2, 'lable'), array('href' => wfArray::get($value2, 'href'), 'onclick' => wfArray::get($value2, 'onclick'), 'id' => wfArray::get($value2, 'id')));
            $li = wfDocument::createHtmlElement('li', array($a), array('class' => self::getActive(wfArray::get($value2, 'href'))), wfArray::get($value2, 'settings'));
            $item[] = $li;
          }

        }
      }
      if(wfArray::isKey($value, 'element')){
        $li = wfDocument::createHtmlElement('li', array($value['element']));
        $item = array($li);
      }



      $ul = wfDocument::createHtmlElement('ul', $item, array('class' => 'nav navbar-nav '.wfArray::get($value, 'class')));
      $navbars[] = $ul;
    }
    $element = wfArray::set($element, 'menu/innerHTML/nav/innerHTML/container/innerHTML/content/innerHTML', $navbars);
    // Rewrite.
    $element = self::rewrite($element, $data);
    // Render.
    wfDocument::renderElement($element);
  }
  
  private static function getActive($href){
    $class = null;
    if(isset($_SERVER['REQUEST_URI'])){
      if(strtolower($_SERVER['REQUEST_URI']) == strtolower($href)){
        $class = 'active';
      }
    }elseif(isset($_SERVER['HTTP_X_ORIGINAL_URL'])){
      if(strtolower($_SERVER['HTTP_X_ORIGINAL_URL']) == strtolower($href)){
        $class = 'active';
      }
    }
    return $class;
  }


  
  /**
  <p>
  Bootstrap Carousel
  </p>
  <p>
  http://www.w3schools.com/bootstrap/bootstrap_carousel.asp
  </p>
  */
  public static function widget_carousel($data){
    $carousel = wfFilesystem::loadYml(dirname(__FILE__).'/data/carousel.yml');
    $carousel_data = null;
    if(wfArray::isKey($data, 'data')){
      $carousel_data = wfArray::get($data, 'data');
      if(!is_array($carousel_data)){
        $carousel_data = wfSettings::getSettingsFromYmlString($carousel_data);
      }
    }else{
      echo 'Param data is missing.';
    }
    
    if(!wfArray::get($carousel_data, 'id')){
      $carousel_data = wfArray::set($carousel_data, 'id', wfCrypt::getUid());
    }
    
    //wfHelp::yml_dump($carousel_data);
    $carousel = wfArray::set($carousel, 'div/attribute/id', wfArray::get($carousel_data, 'id'));
    $carousel = wfArray::set($carousel, 'div/innerHTML/a_left/attribute/href', '#'.wfArray::get($carousel_data, 'id'));
    $carousel = wfArray::set($carousel, 'div/innerHTML/a_right/attribute/href', '#'.wfArray::get($carousel_data, 'id'));
    
    // List element.
    $li = array();
    foreach ($carousel_data['content'] as $key => $value) {
      $attribute = array('data-target' => '#'.wfArray::get($carousel_data, 'id'), 'data-slide-to' => $key);
      if($key==0){
        $attribute = array_merge($attribute, array('class' => 'active'));
      }
      $li[] = wfDocument::createHtmlElement('li', null, $attribute);
    }
    $carousel = wfArray::set($carousel, 'div/innerHTML/ol/innerHTML', $li);
    // Items.
    $div = array();
    foreach ($carousel_data['content'] as $key => $value) {
      if($key==0){
        $attribute = array('class' => 'item active');
      }else{
        $attribute = array('class' => 'item');
      }
      $div[] = wfDocument::createHtmlElement('div', $value, $attribute);
    }
    $carousel = wfArray::set($carousel, 'div/innerHTML/div/innerHTML', $div);
    // Render.
    wfDocument::renderElement(($carousel));
  }
  
  
  
  /**
  <p>
  Carousel for sliding divs with images or other type content.
  </p>
  <p>
  http://www.w3schools.com/bootstrap/bootstrap_carousel.asp
  </p>
  <p>
  Widget settings.
  </p>
  #code-yml#
  type: widget
  data:
    plugin: 'wf/bootstrap'
    method: carousel2
    data: 'yml:/theme/[theme]/data/bootstrap_carousel.yml'
  #code#
  <p>
  Example of data. content is any type of elements.
  </p>
  #code-yml#
  #load:[app_dir]/plugin/[plugin]/data/widget_carousel2_example_data.yml:load#
  #code#
  <p>
  Default data where carousel/innerHTML/ol/innerHTML and carousel/innerHTML/div/innerHTML should will be override by content.
  </p>
  #code-yml#
  #load:[app_dir]/plugin/[plugin]/data/widget_carousel2.yml:load#
  #code#
   */
  public static function widget_carousel2($data){
    // Set data.
    if(wfArray::isKey($data, 'data/id') && wfArray::isKey($data, 'data/content')){
      // Get default carousel yml.
      $widget = wfFilesystem::loadYml(dirname(__FILE__).'/data/widget_carousel2.yml', true, array('_id_' => wfArray::get($data, 'data/id')));
//      // Set ID for carousel.
//      $widget = wfArray::set($widget, 'carousel/attribute/id', wfArray::get($data, 'data/id'));
//      // Set IDs for step links.
//      $widget = wfArray::set($widget, 'carousel/innerHTML/a_left/attribute/href', '#'.wfArray::get($data, 'data/id'));
//      $widget = wfArray::set($widget, 'carousel/innerHTML/a_right/attribute/href', '#'.wfArray::get($data, 'data/id'));
      // List element.
      $li = array();
      foreach (wfArray::get($data, 'data/content') as $key => $value) {
        $attribute = array('data-target' => '#'.wfArray::get($data, 'data/id'), 'data-slide-to' => $key);
        if($key==0){
          $attribute = array_merge($attribute, array('class' => 'active'));
        }
        $li[] = wfDocument::createHtmlElement('li', null, $attribute);
      }
      $widget = wfArray::set($widget, 'carousel/innerHTML/ol/innerHTML', $li);
      // Items.
      $div = array();
      foreach (wfArray::get($data, 'data/content') as $key => $value) {
        if($key==0){
          $attribute = array('class' => 'item active');
        }else{
          $attribute = array('class' => 'item');
        }
        $div[] = wfDocument::createHtmlElement('div', $value, $attribute);
      }
      $widget = wfArray::set($widget, 'carousel/innerHTML/div/innerHTML', $div);
    }else{
      // Get default carousel yml without any changes.
      $widget = wfFilesystem::loadYml(dirname(__FILE__).'/data/widget_carousel2.yml', true, array('_id_' => wfCrypt::getUid()));
    }
    // Override.
    $widget = self::rewrite($widget, $data);
    wfDocument::renderElement(($widget));
  }
  
  
  
  /**
  <p>
  Carousel with img, h3 and p element. 
  </p>
  <p>
  Registration to call widget. 
  </p>
  #code-yml#
  type: widget
  data:
    plugin: 'wf/bootstrap'
    method: carouselimgh3p
    data: 'yml:/theme/[theme]/_data/bootstrap_carousel.yml'
  #code#
  <p>
  Data. Use img_dir to run images registrated via form. Or use the img tag for each item.
  </p>
  #code-yml#
  id: theme_carousel
  img_dir: '/theme/[theme]/img/carousel/[key].jpg'
  content:
    10:
      imgzzz: /theme/wf/hellocompany/carousel/tie-690084_1280.jpg
      lable: Solutionss
      text: 'It´s not my solution - it´s yours.'
    20:
      imgzzz: /theme/wf/hellocompany/carousel/businessman-840623_1920.jpg
      lable: Web
      text: 'It´s a start not the end.'
    30:
      imgzzz: /theme/wf/hellocompany/carousel/office-336368_1920.jpg
      lable: Information
      text: 'Keep it simple. Very.'
    40:
      imgzzz: /theme/wf/hellocompany/carousel/security-265130_1920.jpg
      lable: Security
      text: 'Fast is not always good.'
  #code#
  <p>
  Form yml. 
  </p>
  #code-yml#
  name: 'Carousel'
  file: /theme/[theme]/_data/bootstrap_carousel.yml
  key: content
  multiple_items: true
  form:
    data:
      items:
        lable:
          type: varchar
          lable: Headline
          mandatory: true
        text:
          type: text
          lable: Text
          mandatory: true
  list:
    order_byzzz:
      -
        name: date
        desc: true
    item:
      lable: Headline
      text: Text
  files:
    carousel_img:
      file_type: 'image/jpeg'
      type: jpg
      lable: Image
      dir: '[web_dir]/theme/[theme]/img/carousel'
      name: '[key].jpg'
  #code#
   */
  public static function widget_carouselimgh3p($data){
    // Set data.
    
    if(isset($data['data']['content'])){
      $data['data']['content'] = wfArray::sortMultiple($data['data']['content'], 'sortorder');
    }
    //wfHelp::yml_dump($data);
    

    
    if(wfArray::isKey($data, 'data/id') && wfArray::isKey($data, 'data/content')){
      $item = wfFilesystem::loadYml(__DIR__.'/data/widget_carousel_imgh3p_item.yml', true);
      //wfHelp::yml_dump($item, true);
      // Get default carousel yml.
      $widget = wfFilesystem::loadYml(__DIR__.'/data/widget_carousel_imgh3p.yml', true, array('_id_' => wfArray::get($data, 'data/id')));
      
      // Li.
      $li = array();
      $i = 0;
      foreach (wfArray::get($data, 'data/content') as $key => $value) {
        $temp = $item['li'];
        $temp = wfArray::set($temp, 'attribute/data-target', '#'.wfArray::get($data, 'data/id'));
        $temp = wfArray::set($temp, 'attribute/data-slide-to', $i);
        if($i==0){
          $temp = wfArray::set($temp, 'attribute/class', 'active');
        }else{
          $temp = wfArray::set($temp, 'attribute/class', '');
        }
        $li[] = $temp;
        $i++;
      }
      $widget = wfArray::set($widget, 'carousel/innerHTML/ol/innerHTML', $li);


      // Items.
      $div = array();
      $i = 0;
      foreach (wfArray::get($data, 'data/content') as $key => $value) {
        $temp = $item['div'];
        if($i==0){
          $temp = wfArray::set($temp, 'attribute/class', 'item active');
        }else{
          $temp = wfArray::set($temp, 'attribute/class', 'item');
        }
        
        if(wfArray::get($value, 'img')){
          $temp = wfArray::set($temp, 'innerHTML/img/attribute/src', wfArray::get($value, 'img'));
        }else{
          $temp = wfArray::set($temp, 'innerHTML/img/attribute/src', str_replace('[key]', $key, wfArray::get($data, 'data/img_dir')));
        }
        
        
        $temp = wfArray::set($temp, 'innerHTML/div/innerHTML/h3/innerHTML', wfArray::get($value, 'lable'));
        $temp = wfArray::set($temp, 'innerHTML/div/innerHTML/p/innerHTML', wfArray::get($value, 'text'));
        $div[] = $temp;
        $i++;
      }
      $widget = wfArray::set($widget, 'carousel/innerHTML/div/innerHTML', $div);
      //wfHelp::yml_dump($widget, false);
    }else{
      // Get default carousel yml without any changes.
      $widget = wfFilesystem::loadYml(__DIR__.'/data/widget_carousel_imgh3p.yml', true, array('_id_' => wfCrypt::getUid()));
    }
    // Override.
    $widget = self::rewrite($widget, $data);
    wfDocument::renderElement(($widget));
  }
  

  /**
  <p>
  Default yml.
  </p>
  #code-yml#
  #load:[app_dir]/plugin/[plugin]/data/thumbnail.yml:load#
  #code#
   */
  public static function widget_thumbnail($data){
    // Get default thumbnail.
    $widget = wfFilesystem::loadYml(dirname(__FILE__).'/data/thumbnail.yml');
    // Rewrite thumbnail.
    $widget = self::rewrite($widget, $data);
    // Render element.
    wfDocument::renderElement(($widget));
  }
  
  
  /**
   * This function is depricated. Use wfSettings::rewrite instead.
   */
  private static function rewrite($yml, $data){
    if(wfArray::isKey($data, 'data/rewrite')){
      foreach (wfArray::get($data, 'data/rewrite') as $key => $value) {
        $yml = wfArray::set($yml, $key, $value);
      }
    }
    return $yml;
  }
  
  
  
  
  /**
  <p>
  Default yml.
  </p>
  #code-yml#
  #load:[app_dir]/plugin/[plugin]/data/jumbotron.yml:load#
  #code#
  <p>
  Example of usage.
  </p>
  #code-yml#
  type: widget
  data:
    plugin: 'wf/bootstrap'
    method: 'jumbotron'
    data:
      rewrite:
        'jumbotron/innerHTML/h1/innerHTML': 'About'
        'jumbotron/innerHTML/p1/innerHTML': 'Who are we?'
        'jumbotron/innerHTML/p2/innerHTML/a/attribute/href': '/about'
  #code#
  */
  public static function widget_jumbotron($data){
    // Get default thumbnail.
    $element = wfFilesystem::loadYml(dirname(__FILE__).'/data/jumbotron.yml');
    // Rewrite thumbnail.
    //wfHelp::yml_dump($data, true);
    $element = self::rewrite($element, $data);
    
    // Render element.
    wfDocument::renderElement(($element));
  }
  
  
  
  
}