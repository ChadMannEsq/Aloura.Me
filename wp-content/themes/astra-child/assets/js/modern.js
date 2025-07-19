// Vanilla JS for loading posts
(function(){
  var button = document.getElementById('more_posts');
  if(!button){return;}
  var page = 0;
  var ppp = 6;
  button.addEventListener('click', function(){
    if(button.classList.contains('disabled')){return;}
    page++;
    var type = button.getAttribute('data-type');
    var xhr = new XMLHttpRequest();
    xhr.open('POST', ajax_admin.ajax_url, true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    xhr.onload = function(){
      if(xhr.status === 200){
        var div = document.createElement('div');
        div.innerHTML = xhr.responseText;
        if(div.children.length){
          document.getElementById('ajax-posts').appendChild(div);
        }else{
          button.classList.add('disabled');
        }
      }
    };
    xhr.send('action=more_post_ajax&pageNumber='+page+'&ppp='+ppp+'&type='+type);
  });
})();
