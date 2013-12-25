<?php

require_once("class.php");

$db = new Db();
$message = "";

if(!empty($_POST)){
    if($_POST["mode"]=="add"){
        $name = $_POST["name"];
        $result = $db->addCast($name);
        if($result == 0){
            $message = "登録に失敗しました";
        }
    }
    if($_POST["mode"]=="del"){
        $name = $_POST["name"];
        $db->removeCast($name);
        exit();
    }
    if($_POST["mode"]=="abstract"){
        $members = $db->listMembersByCastName($_POST["cast"]);
        header('Content-Type: application/json; charset=utf-8');
        $db->log($_POST["cast"]);
        print(json_encode($members));
        exit();
    }
}elseif(!empty($_GET["mode"])){
    if($_GET["mode"]=="json"){
        $casts = $db->listCasts();
        header('Content-Type: application/json; charset=utf-8');
        print(json_encode($casts));
        exit();
    }

}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<script src="js/knockout-3.0.0.js"></script>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>

<h3>
fu - matching
<a href="index.php">cast</a>
and
<a href="member.php">member</a>
<a href="#" onclick="$('#help').show()">[?]</a>
</h3>

<div id="help" style="display:none">
<ul>
    <li>キャストを追加する場合は、このページの入力ボックスに名前を入力してボタンを押してください。</li>
    <li>メンバーを追加する場合は、<a href="member.php">member</a>をクリックしてください。</li>
</ul>
</div>

<form method="post" action="index.php">
    <input type="hidden" name="mode" value="add" />
    <input type="input" name="name" />
    <input type="submit" value="キャストを追加する" /><?php echo $message;?>
</form>

<table>
    <tbody data-bind="foreach: choices">
        <tr>
            <td>
                <input type="checkbox" name="casts" data-bind="attr:{value: $data}, checked: $parent.selectedChoices" />
                <span data-bind="text: $data"></span>
            </td>
            <td>
                <a href="#" data-bind="click: $parent.removeCast">Delete</a>
            </td>
        </tr>
    </tbody>
</table>

<hr>
<textarea type="input" data-bind="value: result"></textarea>

<script type="text/javascript">

    casts = [];
    $.ajax({
        async:false,
        url:"./index.php?mode=json",
        type:"GET",
        dataType:"json",
        success: function(json){
            for(i=0;i<json.length;i++){
                casts.push(json[i]);
            }
        }
    });

    var viewModel = {};

    viewModel.choices = casts;
    viewModel.selectedChoices = ko.observableArray();
    viewModel.result = ko.dependentObservable(function () {
        members = [];
        $.ajax({
            async:false,
            url:"./index.php",
            type:"POST",
            data: {
                mode:"abstract",
                cast:viewModel.selectedChoices()
            },
            dataType:"json",
            success: function(json){
                for(i=0;i<json.length;i++){
                    members.push(json[i]);
                }
            }
        });
        return members;
    });

    viewModel.removeCast = function(name){
        $.post("index.php",
            {mode:"del",name:name},
            function(val){
                location.href = "index.php";
            });
    };

    ko.applyBindings(viewModel);

</script>

<a href="index.php">index</a>
<a href="member.php">member</a>
</body>
</html>