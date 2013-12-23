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

<form method="post" action="member.php">
    <input type="hidden" name="mode" value="add">
    <input type="text" name="mail">
    <input type="submit"><?php echo $message;?>
</form>

<ul data-bind="foreach: members">
    <li>
        <a href="#" data-bind="click: $parent.editMember">
        <span data-bind="text: $data"></span>
        </a>
        <a href="#" data-bind="click: $parent.removeMember">[Delete]</a>
    </li>
</ul>


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