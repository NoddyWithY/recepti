<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Uredi recept</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="recepti.css">
        <link rel="icon" type="image/x-icon" href="/slike/recepti_icon.png">
        <script src=" "></script>
    </head>
    <body>
        <div class="nav">
            <a class="gumb" href="recepti.php">Domača stran</a>
            <a class="gumb" href="nov_recept.html">Nov recept</a>
            <a class="gumb" href="uredi_recept.php">Uredi recept</a>
        </div>
        <div class="besedilo">
            <h1>Uredi recept</h1>
            <p>Uredi recept, pri postopku in sestavinah napiši vsako v svojo vrstico.<br>Pri sliki in opombah, če jih ni, napiši "n" ali pusti prazno.</p>
            
            <?php
            ob_start();
            function readJSON() {
                $filePath = "recepti.json";
            
                if (!file_exists($filePath)) {
                    file_put_contents($filePath, json_encode([]));
                }
            
                $data = file_get_contents($filePath);
            
                $decodedData = json_decode($data, true);
            
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo "Error decoding JSON: " . json_last_error_msg();
                    return [];
                }
            
                return $decodedData;
            }
            $podatki = readJSON();
            $option = "";
            foreach($podatki as $naslov => $recept){
                $option.="<option value='$naslov'>$naslov</option>";
            }
            $html = "<form action='uredi_recept.php' method='POST'>
                        <select id='naslovi' name='naslovi'>
                        $option
                        </select>
                        <input type='submit'>
                    </form>
                    ";
            echo $html;
            $naslov = $_POST["naslovi"] ?? null;
            if($naslov !== null){
                $slika = $podatki[$naslov]['slika'];
                $sestavineList = $podatki[$naslov]['sestavine'];
                $korakiList = $podatki[$naslov]['koraki'];
                $sestavine = "";
                $koraki="";
                foreach($sestavineList as $x){
                    $sestavine.=$x."\n";
                }
                foreach($korakiList as $x){
                    $koraki.=$x."\n";
                }
                $opomba = $podatki[$naslov]['opomba'];
                $html = 
    "<form action='recepti.php' method='POST'>
    <h2>Naslov</h2>
    <input type='text' name='naslov' id='naslov' placeholder='Naslov' required value='$naslov'>
    <h2>Slika</h2>
    <input type='text' name='slika' id='slika' placeholder='URL slike ...' value='$slika'>
    <h2>Sestavine</h2>
    <textarea name='sestavine' rows='10' cols='40' placeholder='Sestavine ...' required>$sestavine</textarea>
    <h2>Postopek</h2>
    <textarea name='postopek' rows='10' cols='40' placeholder='Postopek ...' required>$koraki</textarea>
    <h2>Opomba</h2>
    <textarea cols='40' rows='10' type='text' name='opomba' id='opomba' placeholder='Opomba'>$opomba</textarea><br>
    <input type='submit'>
</form>
";
                echo $html;
            };
            ?>
        </div>
    </body>
</html>