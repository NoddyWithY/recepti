#THIS IS NOT NEEDED ANYMORE
#JUST FOR ARCHIVE PURPOSES
#DO NOT USE THIS, IT COULD BREAK SOME STUFF!!!
import os
import json
CUR_DIR = str(os.path.dirname(os.path.realpath(__file__)))
noviPodatki = False
check = input("Clicking y will break some stuff.\nUnless you are Samo DO NOT PRESS Y!!!\n")
if check.lower != "y":
    exit()
def sort(podatki):
    with open(CUR_DIR + "\\recepti.json", "w", encoding="utf-8") as file:
        sorted_data_keys = json.dumps({k: podatki[k] for k in sorted(podatki)})
        sorted_data_keys = json.loads(sorted_data_keys)
        json.dump(sorted_data_keys, file, indent=4, ensure_ascii=False)
def readJSON():
    with open(CUR_DIR + "\\recepti.json", encoding="utf-8") as file:
        podatki = json.load(file)
    return podatki
def toJSON(naslov, slika, sestavine, koraki, opomba):
    podatki = readJSON()
    if opomba.lower() != "n":
        dodatek = {naslov:{"slika": slika, "sestavine": sestavine,"koraki": koraki, "opomba": opomba}}
    else:
        dodatek = {naslov:{"slika": slika, "sestavine": sestavine,"koraki": koraki, "opomba": "n"}}
    podatki.update(dodatek)
    sort(podatki)
def updateMain():
    podatki = readJSON()
    fileList = ""
    kljuci = list((podatki.keys()))
    for i in range(len(podatki.keys())):
        fileList = fileList + f'<li><a href="recepti/{kljuci[i]}.html">{kljuci[i]}</a></li>\n'
    mainFile = f"""<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Recepti</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="recepti.css">
        <script src=" "></script>
    </head>
    <body>
        <div class="nav">
            <a class="gumb" href="recepti.html">Domača stran</a>
            <a class="gumb" href="nov_recept.html">Nov recept</a>
        </div>
        <div id="besedilo">
        <h1 class="naslov">Recepti</h1>
        <p class="besedilo">Poišči recept:</p>
        <ul>
            {fileList}
        </ul>
        </div>
    </body>
</html>
"""
    with open(CUR_DIR + "\\recepti.html", "w", encoding="utf-8") as file:
        file.write(mainFile)
def updateRecepti(podatki):
    for i in range(len(podatki)):
        kljuci = list((podatki.keys()))
        naslov = kljuci[i]
        slika = podatki[naslov]['slika']
        steviloSestavin = len(podatki[naslov]["sestavine"])
        steviloKorakov = len(podatki[naslov]["koraki"])
        sestavineList = podatki[naslov]["sestavine"]
        korakiList = podatki[naslov]["koraki"]
        koraki = ''
        sestavine = ''
        opomba = podatki[naslov]["opomba"]
        if slika.lower() == "n":
            slika = ''
        else:
            slika = f'<p>\n<img src="{slika}" alt="{naslov}" style="float:right">\n</p>'
        for i in range(steviloSestavin):
            sestavine = sestavine + f'<li>{str(sestavineList[i])}</li>\n'
        for i in range(steviloKorakov):
            koraki = koraki + f'<li>{str(korakiList[i])}</li>\n'
        if opomba.lower() != "n":
            opomba = f'<h2 class="manjsiNaslov">Opombe:</h2>\n<p id="opombe">{opomba}</p>'
        else:
            opomba = ''
        recept = f'''
<!DOCTYPE html>
<html lang="en">
    <meta charset="UTF-8">
    <title>{naslov}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../recepti.css">
    <body>
        <div class="nav">
            <a class="gumb" href="../recepti.html">Domača stran</a>
            <a class="gumb" href="../nov_recept.html">Nov recept</a>
        </div>
        <div id="besedilo">
            <h1 id="naslov">{naslov}</h1>
            {slika}
            <h2 class="manjsiNaslov">Sestavine</h2>
            <ul id="sestavine">
                {sestavine}
            </ul>
            <h2 class="manjsiNaslov">Postopek:</h2>
            <ol id="koraki">
                {koraki}
            </ol>
                {opomba}
        </div>
    </body>
</html>
'''
        with open(CUR_DIR + "\\recepti\\" + naslov + ".html", "w", encoding="utf-8") as file:
            file.write(recept)

def pridobiPodatke():
    naslov = str(input("Napiši naslov!"))
    slika = input("Napiši naslov slike. Napiši N če je ni.")
    steviloSestavin = int(input("Koliko sestavin je? "))
    sestavine = []
    for i in range(steviloSestavin):
        sestavina = str(input("Sestavina: "))
        sestavine.append(sestavina)
    steviloKorakov = int(input("Koliko korakov? "))
    koraki = []
    for i in range(steviloKorakov):
        korak = str(input(f"{i+1}. korak: "))
        koraki.append(korak)
    opomba = str(input("Ali so kakšne opombe? Napiši N, če jih ni."))
    toJSON(naslov, slika, sestavine, koraki, opomba)
if noviPodatki:
    pridobiPodatke()
podatki = readJSON()
sort(podatki)
updateRecepti(podatki)
updateMain()