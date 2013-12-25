<?php

require_once("class.php");
$db = new Db();
$message = "";

if(!empty($_POST)){
    if($_POST["mode"]=="add"){
        $mail = $_POST["mail"];
        $result = $db->addMember($mail);
        if($result == 0){
            $message = "登録に失敗しました";
        }
    }
    if($_POST["mode"]=="del"){
        $mail = $_POST["mail"];
        $db->removeMember($mail);
    }
}

$members = $db->listMembers();

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
        <li>メンバーを追加する場合は、このページの入力ボックスに名前を入力してボタンを押してください。</li>
        <li>キャストを追加する場合は、<a href="index.php">cast</a>をクリックしてください。</li>
    </ul>
</div>

<form method="post" action="member.php">
    <input type="hidden" name="mode" value="add">
    <input type="text" name="mail">
    <input type="submit" value="メンバーを追加する"><?php echo $message;?>
</form>

<table>
    <tbody data-bind="foreach: members">
        <tr>
            <td><span data-bind="text: $data"></span></td>
            <td><a href="#" data-bind="click: $parent.editMember">Edit</a></td>
            <td><a href="#" data-bind="click: $parent.removeMember">Delete</a></td>
        </tr>
    </tbody>
</table>

<script>
    var viewModel = {};
    viewModel.members = <?php echo json_encode($members)?>;

    viewModel.editMember = function(member){
        location.href = "edit.php?mail="+member;
    };

    viewModel.removeMember = function(mail){
        $.post("member.php",
            {mode:"del",mail:mail},
            function(val){
                location.href = "member.php";
            });
    };

    ko.applyBindings(viewModel);
</script>

<a href="index.php">index</a>
<a href="member.php">member</a>
</body>
</html>