<?php
function show_contents_in_directory ($dir) {
    //leest alle bestanden uit een map
    $filenames = scandir($dir);
    //haal de eerste 2 filenames eraf uit de lijst (. en ..)
    $filenames = array_splice($filenames, 2);

    foreach ($filenames as $filename) {

        //laat het hele pad zien, de bestandsnaam en alle mappen waar dat bestand in zit
        $full_path = $_GET['chosen_directory'] . "/$filename";
        //type van het bestand
        $mimetype = mime_content_type("eindopdracht/$full_path");

        //als het bestand hidden is en begint met een punt, laat het niet zien en gaat door naar volgende
        if (str_starts_with($filename, '.'))
            continue;

        // is it a dir?
        if (is_dir("eindopdracht/$full_path")) {
            echo "<img src='eindopdracht/pictures/folder2.png' alt='Folder' width='20' height='20'>";
            echo "<a href='?chosen_directory=$full_path'> $filename</a><br>";

        // is it an image?
        } elseif (substr($mimetype, 0, 5) === 'image'){
            echo "<img src='eindopdracht/pictures/image2.png' alt='Image' width='20' height='20'>";
            echo "<a href='?chosen_directory=" . $_GET['chosen_directory'] . "&chosen_file=$full_path'> $filename</a><br>";

        // is it a file?
        } else {
            echo "<img src='eindopdracht/pictures/document2.png' alt='document' width='20' height='20'>";
            echo "<a href='?chosen_directory=" . $_GET['chosen_directory'] . "&chosen_file=$full_path'> $filename</a><br>";
        }
    }
}
// beveiliging zodat je niet hoger dan je huidige map kunt
//stristr gaat op zoek naar de 'needle' in de 'haystack'. Waarvan chosen_directory de haystack is en de 'needle' de '..' Als hij die 'needle' heeft gevonden stopt alles door die
if (stristr($_GET['chosen_directory'], "..") || stristr($_GET['chosen_file'], "..")) {
    ?>
    <h1>Access denied bitch</h1>
    <?php
    die();
}

//update het bestand in chosen_file met de nieuwe ingevulde tekst
if (isset ($_POST ["file_content"])) {
    $file_location = "eindopdracht/" . $_GET['chosen_file'];
    $new_file_content = $_POST ["file_content"];

    file_put_contents($file_location, $new_file_content);
}
?>
<html>
    <head>
    </head>
    <body class="background=">

    <title>Filebrowser</title>
    <link href="eindopdracht.css" rel="stylesheet">

    <ul class="breadcrumbs">
        <li class="breadcrumbs_item">
            <a href="eindopdracht.php" class="breadcrumbs_link">Home</a>
        </li>
    <?php
        //hakt je mappenstructuur in stukjes
        $breadcrumbs = explode("/", $_GET ['chosen_directory']);
        //chosen_directory begint met een / dus de eerste is altijd leeg
        $breadcrumbs = array_splice($breadcrumbs, 1);

        $breadcrumb_path = "";
        foreach ($breadcrumbs as $breadcrumb) {
            //voegt de map toe aan het breadcrumb pad om de juiste link te maken
            $breadcrumb_path .= "/$breadcrumb";
            ?>
            <li class="breadcrumbs_item">
                <a href="eindopdracht.php?chosen_directory=<?php echo $breadcrumb_path ?>" class="breadcrumbs_link"><?php echo $breadcrumb ?></a>
            </li>
            <?php
        }
        ?>
    </ul>

        <div class="mappen">
            <h3>Mappen/Bestanden:</h3>
            <?php
                if (isset($_GET['chosen_directory'])) {
                    show_contents_in_directory("eindopdracht/" . $_GET ['chosen_directory']);
                } else {
                    show_contents_in_directory("eindopdracht");
                }
            ?>
        </div>
        <div class="file_info">
            <?php
                if (isset($_GET['chosen_file'])) {
                    $file_location = "eindopdracht/" . $_GET['chosen_file'];
                    ?>
                        <h3>Inhoud:</h3>
                        Bestandsnaam: <?php echo basename ($_GET['chosen_file']); ?><br />
                        Grootte: <?php echo filesize($file_location); ?> bytes<br />
                        Schrijfbaar: <?php
                            if (is_writable($file_location) === true) {
                                echo 'Ja';
                            }
                            else {
                                echo 'Nee';
                            }
                        ?><br />
                        Laatst aangepast: <?php echo date("d F Y H:i:s", filemtime($file_location)); ?><br />

                    <?php
                        $mimetype = mime_content_type($file_location);
                        if (substr($mimetype, 0, 5) === 'image'){
                            echo "<img class='show_image' src='$file_location'>";
                        }
                        else {
                            ?>
                            <form action=" " method="POST">
                                Change the file here:
                                <textarea name='file_content' rows='20' cols='50'><?php echo file_get_contents($file_location); ?></textarea><br>
                                <input class="button" type="submit">
                            </form>
                            <?php
                        }
                }
                        ?><br />
        </div>
    </body>
</html>