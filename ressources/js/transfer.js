jQuery(document).ready(function($){
   $('#btnImport').click(function(e){
       e.preventDefault();
       $('#selectedFile').click();
   });

   $('#selectedFile').change(function(){
       $('#transferForm').submit();
   });

});