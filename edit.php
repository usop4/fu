<?php

require_once("class.php");
$db = new Db();

if(isset($_GET["mail"])){
    $casts = $db->listCasts();
    $selectedCasts = $db->listCastsByMember([$_GET["mail"]]);
}

if(isset($_POST["mode"])){
    if($_POST["mode"]=="addMatch"){
        $result = $db->addMatch($_POST["name"],$_POST["mail"]);
        $selectedCasts = $db->listCastsByMember([$_POST["mail"]]);
        header('Content-Type: application/json; charset=utf-8');
        print(json_encode($selectedCasts));
        exit();
    }
    if($_POST["mode"]=="removeMatch"){
        $result = $db->removeMatch($_POST["name"],$_POST["mail"]);
        $selectedCasts = $db->listCastsByMember([$_POST["mail"]]);
        header('Content-Type: application/json; charset=utf-8');
        print(json_encode($selectedCasts));
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

<h1><?php echo $_GET["mail"];?></h1>

<table>
    <tbody data-bind="foreach: choices">
    <tr>
        <td>
            <input type="checkbox" name="casts" data-bind="attr:{value: $data}, checked: $parent.selectedChoices" />
            <span data-bind="text: $data"></span>
        </td>
    </tr>
    </tbody>
</table>

<div data-bind="text: result"></div>

<script type="text/javascript">

    initialChoices = <?php echo json_encode($selectedCasts);?>;

    var viewModel = {};
    viewModel.choices = <?php echo json_encode($casts);?>;
    viewModel.selectedChoices = ko.observableArray(<?php echo json_encode($selectedCasts);?>);

    viewModel.result = ko.dependentObservable(function(){
        var casts = viewModel.selectedChoices();
        mail = "<?php echo $_GET["mail"];?>";
        for(i=0;i<initialChoices.length;i++){
            if($.inArray(initialChoices[i],casts)==-1){
                $.ajax({
                    url:"edit.php",
                    type:"post",
                    data:{
                        mode:"removeMatch",
                        name:initialChoices[i],
                        mail:mail
                    },
                    dataType:"json",
                    success: function(json){
                        console.log("removeMatch");
                        console.log(json);
                        initialChoices = json;
                    }
                 });
            }
        }
        for(i=0;i<casts.length;i++){
            if($.inArray(casts[i],initialChoices)==-1){
                $.ajax({
                    url:"edit.php",
                    type:"post",
                    data:{
                        mode:"addMatch",
                        name:casts[i],
                        mail:mail
                    },
                    dataType:"json",
                    success: function(json){
                        console.log("addMatch");
                        console.log(json);
                        initialChoices = json;
                    }
                });
            }
        }
        return viewModel.selectedChoices().join(",");
    });

    ko.applyBindings(viewModel);

</script>
<a href="index.php">index</a>
<a href="member.php">member</a>
</body>
</html>