import $ from 'jquery'

$(($) => {

  // フォントテーマ設定
  $('#fontThemeSelect').change(function(){
    let selectTheme = $(this).val();
    // 初期非表示
    $('#customeFontThemeForm').hide();
    $('#fontThemeDeleteButton').hide();
    // 新しくテーマを作成する。表示
    if (selectTheme === 'new') {
      $('input[name=ts_edit_mode]').val('new');
      $('#ts_custome_theme_id').val(unique_id);
      $("<input>",{
          type:"hidden",
          name:"ts_change_edit_theme",
          value:"change"
        }).appendTo("#custmeFontForm");
      $('#custmeFontForm').submit();
    // カスタムテーマ選択。表示
  } else if(selectTheme !== 'false') {
    $('#custome_font_name').removeAttr('required');
    $('input[name=ts_edit_mode]').val('update');
    $('#ts_custome_theme_id').val('');
      const custome_fonts = option_font_list.theme;
      $.each(custome_fonts, function(i,cs_font){
        if(cs_font.id === selectTheme){
          $('input[name=ts_edit_mode]').val('update');
          $('#ts_custome_theme_id').val(cs_font.id);
          $("<input>",{
              type:"hidden",
              name:"ts_change_edit_theme",
              value:"change"
            }).appendTo("#custmeFontForm");
          $('#custmeFontForm').submit();
        }
      })
    }else{
      $('#custome_font_name').removeAttr('required');
      $('input[name=ts_edit_mode]').val('update');
      $('#ts_custome_theme_id').val('');
      $('#fontThemeDeleteButton').hide();
    }
  })

  // フォントテーマ更新ボタン押下処理
  $('#fontThemeUpdateButton').click(function(){
    const nameDuplicateMessage = "同一名のカスタムフォントテーマは作成できません";
    const countOverMessage = "カスタムフォントテーマは10個を超えて作成できません";

    let warningFlg = 0;

    // カスタムテーマ以外の選択
    if($('#customeFontThemeForm').is(':hidden')){
      return true;
    }

    // 同一名チェック
    const all_font_theme = all_font_list;
    const custome_font_name = $('#custome_font_name').val();
    if (custome_font_name !== $('#current_custome_font_name').val()) {
      $.each(all_font_theme, function(i,af_font){
        if (af_font.name === custome_font_name) {
          warningFlg = 1;
          return false;
        }
      })
    }
    // 個数チェック
    const custome_fonts = option_font_list.theme;
    const custome_font_count = Object.keys(custome_fonts).length;
    if (custome_font_count >= 10 && !$('#current_custome_font_name').val()) {
      warningFlg = 2;
    }

    // エラー切り分け
    switch (warningFlg) {
      case 1: // 同一名警告
        alert(nameDuplicateMessage);
        return false;
        break;
      case 2: // １１個以上警告
        alert(countOverMessage);
        return false;
        break;
      default:
      if ($('#custome_font_name').val()) {
        $('#custmeFontForm').submit();
      }
    }
  })

  // フォントテーマ削除ボタン押下処理
  $('#fontThemeDeleteButton').click(function(){
    const dialogMessage = $('#current_custome_font_name').val() + 'を削除します。よろしいですか？';
    const fontDeleteConfirmDialog = window.confirm(dialogMessage);

    if(fontDeleteConfirmDialog){
      $('input[name=ts_edit_mode]').val('delete');
      $('#custmeFontForm').submit();
    }else{
      return false;
    }
  })

  // localStorageのactiveAdvancedを取得
  const activeAdvanced = localStorage.getItem('activeAdvanced')
  if (activeAdvanced === 'true') {
    $('.ts-custome_form').css('display', 'block')
    $('.ts-custome_form').addClass('ts-active')
    $('.advancedTriangle').addClass('open')
  }


  // 「上級者向けのカスタマイズ」クリック時イベント
  $(".toggleAdvanced").on('click', () => {
    $('.ts-custome_form').slideToggle('normal', () => {
      if ($('.ts-custome_form').hasClass('ts-active')) {
        $('.ts-custome_form').removeClass('ts-active')
        $('.advancedTriangle').removeClass('open')
        localStorage.setItem('activeAdvanced', false)
      } else {
        $('.ts-custome_form').addClass('ts-active')
        $('.advancedTriangle').addClass('open')
        localStorage.setItem('activeAdvanced', true)
      }
    })
  })
})
