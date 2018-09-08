<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <form id="" action="" method="post" enctype="multipart/form-data" accept-charset="utf=8">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <span>Select File..&nbsp;</span>
            <input name="file" type="file"/>
            <input type="submit" value="Upload"/>
        </form>
    </body>
</html>
