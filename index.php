<?php
include 'syspkg.php';
$mode = ($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
if ($mode == 'app') {
    $dir = '.';
    $list = str_replace($dir.'/','',(glob($dir.'/*.app')));
} elseif ($mode == 'pkg') {
    $dir = '.';
    $list = str_replace($dir.'/','',(glob($dir.'/*.pkg')));
} elseif ($mode == 'font') {
    $fontfile = $_REQUEST['name'];
} elseif ($mode == 'watch') {
    $name = $_REQUEST['name'];
} elseif ($mode == 'view') {
    $name = $_REQUEST['name'];
} elseif ($mode == 'edit') {
    $editname = $_REQUEST['name'];
    $lock = $_REQUEST['lock'];
    if ($lock != 'true') {
        $content = file_get_contents($editname);
    }
} elseif ($mode == 'glob') {
    $dir = ($_REQUEST['dir']) ? $_REQUEST['dir'] : '.';
    $q = ($_REQUEST['q']) ? $_REQUEST['q'] : '';
    if ($q != '') {
        if ($q == '/') {
            $glob = glob($dir.'/*', GLOB_ONLYDIR);
        } elseif ($q == '*') {
            $glob = glob($dir.'/*');
        } else {
            $glob = glob($dir.'/*{'.$q.'}*', GLOB_BRACE);
        }
    } else {
        $glob = glob($dir.'/*');
    }
    $list = str_replace($dir.'/','',($glob));
    usort($list, function ($a, $b) {
        $aDirMod = $GLOBALS['dir'].'/'.$a;
        $bDirMod = $GLOBALS['dir'].'/'.$b;
        $aIsDir = is_dir($aDirMod);
        $bIsDir = is_dir($bDirMod);
        if ($aIsDir === $bIsDir)
            return strnatcasecmp($aDirMod, $bDirMod);
        elseif ($aIsDir && !$bIsDir)
            return -1;
        elseif (!$aIsDir && $bIsDir)
            return 1;
    });
    function cutString($value, $piece) {
        return (strlen($value) > $piece) ? mb_strimwidth($value, 0, $piece, '...', 'UTF-8') : $value;
    }
} else {
    $dir = '.';
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<title>Microsoft Web</title>
<link rel="shortcut icon" href="favicon.png?rev=<?=time();?>" type="image/x-icon">
<style>
@font-face {
    font-family: "segoeui";
    src: url("segoeui.ttf");
}
@font-face {
    font-family: "userDefine";
    src: url("<?=$fontfile;?>");
}
body, font, a, p, b, i, strong, em, li {
    background: linear-gradient(to bottom, #c5ddf3 0%, #7aa1e6 100%);
    background-size: 100%;
    color: #000;
    font-family: "segoeui";
    font-size: 14pt;
}
.userDefine {
    color: #000;
    font-family: "userDefine";
    font-size: 20pt;
}
table, tr, td, th {
    background-color: #b6ccff;
    color: #000;
    font-family: "segoeui";
    font-size: 14pt;
    text-align: center;
}
input, select, textarea {
    background-color: #fff;
    color: #000;
    border: none;
    border-radius: 5px;
    font-family: "segoeui";
    font-size: 14pt;
}
.top {
    border: none;
    position: absolute;
    width: 96%;
    height: 15%;
    top: 0%;
    left: 1%;
}
.panel {
    border: none;
    position: absolute;
    background-image: none;
    background-size: auto 92%;
    background-repeat: no-repeat;
    width: 96%;
    height: 70%;
    top: 15%;
    left: 2%;
    overflow-y: scroll;
}
.bottom {
    border: none;
    position: absolute;
    width: 96%;
    height: 15%;
    top: 85%;
    left: 1%;
}
.hover {
    opacity: 0.8;
}
.hover:hover {
    opacity: 0.5;
}
.actionButton {
    background: linear-gradient(to bottom, #6a82db 0%, #1855c4 100%);
    background-size: 100%;
    color: #fff;
    border: none;
    border-radius: 5px;
    width: 29px;
    height: 28px;
    font-family: "segoeui";
    font-weight: bold;
    font-size: 14pt;
    position: relative;
}
.inputPanel {
    background-color: #fff;
    color: #000;
    border: none;
    text-align: center;
    position: relative;
    font-family: "segoeui";
    font-size: 16pt;
    overflow-x: scroll;
    width: 90%;
    height: 40%;
}
.outputPanel {
    background-color: #fff;
    color: #000;
    border: none;
    text-align: center;
    position: relative;
    font-family: "segoeui";
    font-size: 16pt;
    overflow-x: scroll;
    width: 90%;
    height: 40%;
}
.actionIcon {
    height: 10%;
    position: relative;
}
</style>
<?php include 'include.php'; ?>
<script>
window.onload = function() {
<?php if ($mode == 'glob') { ?>
    document.getElementById('search').focus();
<?php } elseif ($mode == 'edit') { ?>
    countText();
<?php } else { ?>
    document.getElementById('enterSeq').focus();
<?php } ?>
}
</script>
</head>
<body>
<div class='top'>
<p align='center'>
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:70%;position:relative;" src="logo.png?rev=<?=time();?>" onclick="window.location.href='index.php';">
</p>
</div>
<div class='panel'>
<?php
if ($mode == 'app') {
    foreach ($list as $key=>$value) {
        $appCont = file_get_contents($value);
        $appExp = explode('|[1]|', $appCont);
        $appTitle = $appExp[0];
        $appIcon = (file_exists($appExp[1])) ? $appExp[1] : 'sys.app.png';
        $appAction = $appExp[2];
?>
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:18%;position:relative;" src="<?=$appIcon;?>?rev=<?=time();?>" title="<?=$appTitle;?>" onclick="<?=$appAction;?>">
<?php
}} elseif ($mode == 'pkg') {
    foreach ($list as $key=>$value) {
        $pkgName = basename($value, '.pkg');
        $pkgCont = file_get_contents($value);
        $pkgExp = explode('|[1]|', $pkgCont);
        $pkgHead = $pkgExp[0];
        $pkgBody = $pkgExp[1];
        $pkgHeadExp = explode('|[2]|', $pkgHead);
        $pkgUser = $pkgHeadExp[0];
        $pkgType = $pkgHeadExp[1];
        $pkgVersion = $pkgHeadExp[2];
        $pkgBuild = $pkgHeadExp[3];
        $pkgCreated = $pkgHeadExp[4];
        $pkgDescription = $pkgHeadExp[5];
?>
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:18%;position:relative;" src="sys.pkg.png?rev=<?=time();?>" name="<?=$pkgName;?>" title="<?=$pkgName;?>" onclick="get('d', '', this.name, 'from', '', 'here');">
<?php
}} elseif ($mode == 'glob') {
    foreach ($list as $key=>$value) {
        $extension = pathinfo($value, PATHINFO_EXTENSION);
        $basename = basename($value, '.'.$extension);
        $size = filesize($dir.'/'.$value);
        $perms = substr(sprintf('%o', fileperms($dir.'/'.$value)), -4);
        $dispName = cutString($value, 15);
        if (is_dir($dir.'/'.$value)) {
            $icon = 'sys.dir.png';
            $link = "window.location.href='index.php?mode=glob&dir=".$dir.'/'.$value."';";
            $type = 'Directory';
        } else {
            if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'webp') {
                $icon = $dir.'/'.$value;
                $link = "window.location.href='".$dir.'/'.$value."';";
                $type = 'Image';
            } elseif ($extension == 'pkg') {
                $icon = 'sys.pkg.png';
                $link = "get('d', '', '".$basename."', 'from', '', 'here');";
                $type = 'Package';
            } elseif ($extension == 'app') {
                $appOpen = file_get_contents($value);
                $appDel = explode('|[1]|', $appOpen);
                $appTitle = $appDel[0];
                $appIcon = $appDel[1];
                $appLink = $appDel[2];
                $icon = (file_exists($appIcon)) ? $appIcon : 'sys.app.png';
                $link = $appLink;
                $type = 'App Link';
            } elseif ($extension == 'mid' || $extension == 'midi' || $extension == 'rmi') {
                $icon = 'sys.mid.png';
                $link = "playMIDI('".$dir.'/'.$value."');";
                $type = 'MIDI';
            } elseif ($extension == 'mp3' || $extension == 'aac' || $extension == 'flac' || $extension == 'mka' || $extension == 'ogg' || $extension == 'wav' || $extension == 'm4a' || $extension == 'wma') {
                $icon = 'sys.aud.png';
                $link = "playAudio(audioPlayer, '".$dir.'/'.$value."');";
                $type = 'Audio';
            } elseif ($extension == 'mp4' || $extension == 'mkv' || $extension == 'webm' || $extension == 'mpg' || $extension == 'mpeg' || $extension == 'avi' || $extension == 'wmv') {
                $icon = 'sys.vid.png';
                $link = "window.location.href='index.php?mode=watch&name=".$dir.'/'.$value."';";
                $type = 'Video';
            } elseif ($extension == 'ttf' || $extension == 'otf' || $extension == 'ttc' || $extension == 'woff2') {
                $icon = 'sys.fon.png';
                $link = "window.location.href='index.php?mode=font&name=".$dir.'/'.$value."';";
                $type = 'Font';
            } elseif ($extension == 'txt' || $extension == 'csv' || $extension == 'md' || $extension == 'css' || $extension == 'js') {
                $icon = 'sys.txt.png';
                $link = "window.location.href='index.php?mode=edit&name=".$dir.'/'.$value."&lock=true';";
                $type = 'Text';
            } else {
                $icon = 'sys.exe.png';
                $link = "window.location.href='".$dir.'/'.$value."';";
                $type = 'Executable';
            }
        }
?>
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:18%;position:relative;" src="<?=$icon;?>?rev=<?=time();?>" name="<?=$value;?>" title="<?=$value;?>" onclick="<?=$link;?>">
<?php }} elseif ($mode == 'get') { ?>
<p align='center'>Execute GET sequence command:<br><input type='text' style="width:45%;position:relative;" value='' onkeydown="if (event.keyCode == 13) {
    eval(this.value);
}"></p>
<?php } elseif ($mode == 'font') { ?>
<p align='center' class='userDefine'>0 1 2 3 4 5 6 7 8 9 A B C D E F G H I J K L M N O P Q R S T U V W X Y Z a b c d e f g h i j k l m n o p q r s t u v w x y z</p>
<?php } elseif ($mode == 'watch') { ?>
<video style="width:100%;height:100%;" id="video" src="<?=$name;?>" controls autoplay="yes">
<?php } elseif ($mode == 'set') { ?>
<p align="center">
<input type='button' id="muteSound" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" name="<?=$sounds;?>" value="<?php if ($sounds) { ?>Mute<?php } else { ?>Unmute<?php } ?>" onclick="mute(muteSound.name);">
</p>
<?php } elseif ($mode == 'edit') { ?>
<img class="actionIcon" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" src="sys.cl.png?rev=<?=time();?>" id="newButton" onclick="var name = 'file'; window.location.href='index.php?mode=edit&name='+name;">
<img class="actionIcon" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" src="sys.rd.png?rev=<?=time();?>" id="openButton" onclick="var name = filename.value; window.location.href = 'index.php?mode=edit&name=' + name + '&lock=false';">
<img class="actionIcon" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" src="sys.wr.png?rev=<?=time();?>" id="saveButton" onclick="save();">
<img class="actionIcon" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" src="sys.md.png?rev=<?=time();?>" id="mkdirButton" onclick="var name = filename.value; mkdir(name, false);">
<img class="actionIcon" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" src="sys.mv.png?rev=<?=time();?>" id="moveButton" onclick="var name = filename.value; var to = doto.value; move(name, to, false);">
<img class="actionIcon" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" src="sys.cp.png?rev=<?=time();?>" id="copyButton" onclick="var name = filename.value; var to = doto.value; copy(name, to, false);">
<img class="actionIcon" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" src="sys.rm.png?rev=<?=time();?>" id="deleteButton" onclick="var name = filename.value; del(name, false);">
<img class="actionIcon" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" src="sys.home.png?rev=<?=time();?>" id="homeButton" onclick="window.location.href = 'index.php';"><br>
<label>Filename: </label>
<input class="text" size=30 id="filename" style="width:38%;" type="text" value="<?=$editname;?>">
<input class="text" size=30 id="doto" style="width:38%;" type="text" value="">
<textarea class="text" id="content" style="width:100%;height:70%;" oninput="countText();"><?=$content;?></textarea><br>
<input class="text" size=30 id="findbox" style="width:36%;" type="text" value="">
<label> to </label>
<input class="text" size=30 id="replacebox" style="width:36%;" type="text" value="">
<input type="button" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" class="actionButton" value=">" id="replaceButton" onclick="replaceText(findbox.value); countText();">
<br>
<label id="statusBar" style="width:98%;"></label>
<?php } elseif ($mode == 'menu') { ?>
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.back.png?rev=<?=time();?>" title="Background Preview" onclick="window.location.href = 'index.php?mode=view';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.files.png?rev=<?=time();?>" title="File Explorer" onclick="window.location.href = 'index.php?mode=glob';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.fon.png?rev=<?=time();?>" title="Fonts" onclick="window.location.href = 'index.php?mode=glob&q=.ttf,.otf,.ttc';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.setup.png?rev=<?=time();?>" title="Install Software" onclick="window.location.href = 'index.php?mode=get';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;"  src="sys.apps.png?rev=<?=time();?>" title="Installed Programs" onclick="window.location.href = 'index.php?mode=app';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.txt.png?rev=<?=time();?>" title="My Documents" onclick="window.location.href = 'index.php?mode=glob&q=.txt,.pdf,.odt,.doc';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.aud.png?rev=<?=time();?>" title="My Music" onclick="window.location.href = 'index.php?mode=glob&q=.mp3,.aac,.flac,.mid';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.img.png?rev=<?=time();?>" title="My Pictures" onclick="window.location.href = 'index.php?mode=glob&q=.png,.gif,.svg,.jpg';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.vid.png?rev=<?=time();?>" title="My Videos" onclick="window.location.href = 'index.php?mode=glob&q=.mp4,.webm,.mkv';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.edit.png?rev=<?=time();?>" title="Notepad" onclick="window.location.href = 'index.php?mode=edit&name=file&lock=true';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.pkg.png?rev=<?=time();?>" title="Remove Software" onclick="window.location.href = 'index.php?mode=pkg';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:22%;position:relative;" src="sys.settings.png?rev=<?=time();?>" title="System Settings" onclick="window.location.href = 'index.php?mode=set';">
<?php } ?>
</div>
<div class='bottom'>
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:84%;position:relative;" src="sys.start.png?rev=<?=time();?>" onclick="window.location.href = 'index.php?mode=menu';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:84%;position:relative;" src="sys.back.png?rev=<?=time();?>" onclick="window.location.href = 'index.php?mode=view';">
<img class="hover" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" style="height:84%;position:relative;" src="sys.settings.png?rev=<?=time();?>" onclick="window.location.href = 'index.php?mode=set';">
<img class="hover" style="height:84%;position:relative;" src="sys.upd.png?rev=<?=time();?>" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" onclick="get('i','','from','windos','','flossely');">
<img class="hover" style="height:84%;position:relative;" src="sys.exit.png?rev=<?=time();?>" onmouseover="playAudio(soundPlayer, '<?=$soundlib[rand(0,$soundct)];?>?rev=<?=time();?>');" onclick="window.location.href = '../';">
</div>
<audio id="audioPlayer">
<audio id="soundPlayer" <?php if (!$sounds) { ?>muted="muted"<?php } ?>>
</body>
</html>
