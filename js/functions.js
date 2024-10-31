// JavaScript Document
function fkapat(haric,slider_sayisi){
	ac('#resim_'+haric);
	jQuery('#alt_yazi_'+haric).removeAttr('class');
	jQuery('#alt_yazi_'+haric).addClass('alt_yazi_genel_secili');
	jQuery('#rs_akindil_galeri').data('key',haric);
	for(i=1;i<=slider_sayisi;i++){
		if(i != haric){
			kapat('#resim_'+i);
			jQuery('#alt_yazi_'+i).removeAttr('class');
			jQuery('#alt_yazi_'+i).addClass('alt_yazi_genel');
		}
	}			
}
function oto_hareket(slider_sayisi){
	 var say = 0;
    (function slides() {
      setTimeout(function() {
        if (say++ < slider_sayisi) {
          fkapat(say,slider_sayisi)
          slides();
		if(say == slider_sayisi){
			say = 0;
		}
        }
      }, 3000);
    })();
}
function ac(div){
	jQuery(div).css('display','block');
}
function kapat(div){
	jQuery(div).css('display','none');
}