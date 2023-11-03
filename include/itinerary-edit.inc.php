<?php
    RegisterLog('Visit:Edit Itinerary-' . $id . ' ' . $itinerary['slug']);
    $_SESSION['auth']['itinerary-edit-id'] = $id;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>A editar <?= htmlspecialchars($itinerary['title']) ?> - MyItinerary</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/favicon-16x16.png">
    <meta name="author" content="Tomás Russo">
    <meta name="theme-color" content="#40916c">

    <!-- Fonte -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;450;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/bootstrap/bootstrap.min.css">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/fontawesome/css/all.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/main.css">
</head>
<body>
    <!-- Menu -->
    <div class="menu menu-white">
        <?php include SITE_DIR . '/include/navbar-only-help.inc.php'; ?>
    </div>

    <div class="itinerary-page itinerary-edit">
        <div class="header" style="background-image: url(<?= SITE_URL ?>/images/background-dark.png), url(<?= SITE_URL ?>/<?= htmlspecialchars($itinerary['wallpaperPath']) ?>);">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col">
                        <h1><?= htmlspecialchars($itinerary['title']) ?></h1>
                        <h4><?php if ($itinerary['isPrivate']): ?><i class="fas fa-lock" title="Visibilidade"></i>Privado&nbsp;&nbsp;&nbsp;&nbsp;<?php else: ?><i class="fas fa-users" title="Visibilidade"></i>Público&nbsp;&nbsp;&nbsp;&nbsp;<?php endif; ?><i class="fas fa-calendar-alt" title="Duração"></i><?= htmlspecialchars($itinerary['duration']) ?> dia<?php if ($itinerary['duration'] != 1) echo 's'; ?>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-map-marker-alt" title="Cidades"></i><?= htmlspecialchars($cities) ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="itin-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-12 main-content">
                        <div class="row buttons">
                            <div class="col">
                                <button onclick="DisableButton(this); SetLoading(this); SaveAndExit();" class="btn btn-primary" id="save-button"><i class="fas fa-save"></i>Guardar e Sair</button>
                                <a href="<?= SITE_URL . '/itinerary/' . $itinerary['id'] . '/' . $itinerary['slug'] ?>" class="btn btn-secondary">Sair</a>
                                <!-- <a href="#" class="btn btn-secondary"><i class="fas fa-sync-alt"></i>Personalizar</a> -->
                                <!-- <a href="#" class="btn btn-white"><i class="fas fa-share-alt"></i>Partilhar</a> -->
                            </div>
                        </div>
                        <hr>
                        <div class="row itinerary">
                            <?php if (is_null($itinerary['contentJSON']) || empty($itinerary['contentJSON'])): ?>
                            <?php for ($i = 1; $i <= $itinerary['duration']; $i++): ?>
                            <h4 class="divider" id="divider"><i class="fas fa-chevron-down" id="icon"></i><a onclick="ChangeArrow(this)" class="link" data-bs-toggle="collapse" href="#collapse-day-<?= $i ?>" role="button" aria-expanded="false" aria-controls="collapse-day-<?= $i ?>">Dia <?= $i ?></a></h4>
                            <div class="collapse show" id="collapse-day-<?= $i ?>">
                                <div id="day-<?= $i ?>">
                                </div>
                                <div class="col buttons" id="buttons-<?= $i ?>">
                                    <button onclick="AddParagraph('day-<?= $i ?>')" class="btn btn-white"><i class="fas fa-plus"></i>Parágrafo</button>
                                    <button onclick="AddLocal('day-<?= $i ?>')" class="btn btn-white"><i class="fas fa-plus"></i>Local</button>
                                    <button onclick="AddImage('day-<?= $i ?>')" class="btn btn-white"><i class="fas fa-plus"></i>Imagem</button>
                                </div>
                            </div>
                            <?php endfor; else: ?>
                            <?php 
                                $arrayJSON = json_decode($itinerary['contentJSON'], true);
                                $content = $arrayJSON['content'];
                                echo JsonToHtmlEdit($content, $itinerary['duration']);
                            ?>
                            <?php endif; ?>
                            <!-- <h4 class="divider" id="divider"><i class="fas fa-chevron-down" id="icon"></i><a onclick="ChangeArrow(this)" class="link" data-bs-toggle="collapse" href="#collapse-day-1" role="button" aria-expanded="false" aria-controls="collapse-day-1">Dia 1</a></h4>
                            <div class="collapse show" id="collapse-day-1">
                                <div id="day-1">
                                    <div class="card paragraph" id="1">
                                        <div class="d-flex flex-column flex-md-row">
                                            <div class="paragraph-text d-flex flex-grow-1">
                                                <textarea onblur="SaveContent('1', this)" class="w-100" name="textarea-1" id="textarea-1" placeholder="Escreva alguma coisa..."></textarea>
                                            </div>
                                            <div class="elements-options d-flex flex-row flex-md-column justify-content-start align-items-end">
                                                <button class="btn" onclick="GoUp('1')"><i class="fas fa-chevron-up"></i></button>
                                                <button class="btn" onclick="GoDown('1')"><i class="fas fa-chevron-down"></i></button>
                                                <button class="btn" onclick="RemoveElement('1')"><i class="fas fa-trash-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card image" id="2">
                                        <div class="d-flex flex-column flex-md-row justify-content-start">
                                            <div class="wrapper-image">
                                                <img class="img-fluid" src="<?= SITE_URL ?>/images/lagos.jpg" alt="">
                                            </div>
                                            <div class="elements-options d-flex flex-row flex-md-column justify-content-start align-items-end">
                                                <button class="btn" onclick="GoUp('2')"><i class="fas fa-chevron-up"></i></button>
                                                <button class="btn" onclick="GoDown('2')"><i class="fas fa-chevron-down"></i></button>
                                                <button class="btn" onclick="RemoveElement('2')"><i class="fas fa-trash-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card image" id="3">
                                        <div class="d-flex flex-column flex-md-row justify-content-start">
                                            <div class="wrapper-image">
                                                <img class="img-fluid" src="<?= SITE_URL ?>/images/natas.jpg" alt="">
                                            </div>
                                            <div class="elements-options d-flex flex-row flex-md-column justify-content-start align-items-end">
                                                <button class="btn" onclick="GoUp('3')"><i class="fas fa-chevron-up"></i></button>
                                                <button class="btn" onclick="GoDown('3')"><i class="fas fa-chevron-down"></i></button>
                                                <button class="btn" onclick="RemoveElement('3')"><i class="fas fa-trash-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card local" id="4">
                                        <div class="d-flex flex-column flex-md-row justify-content-start">
                                            <div class="row">
                                                <div class="col-md-3 align-self-center">
                                                    <a href="https://www.google.com/maps/place/Past%C3%A9is+de+Bel%C3%A9m/@38.6975105,-9.2032276,15z/data=!4m5!3m4!1s0x0:0xffeff6c6b46d9665!8m2!3d38.6975105!4d-9.2032276" target="_blank"><img class="img-fluid" src="<?= SITE_URL ?>/images/pasteis-belem.jpg" alt="Pasteis de Belem"></a>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="card-body">
                                                        <a href="https://www.google.com/maps/place/Past%C3%A9is+de+Bel%C3%A9m/@38.6975105,-9.2032276,15z/data=!4m5!3m4!1s0x0:0xffeff6c6b46d9665!8m2!3d38.6975105!4d-9.2032276" class="link" target="_blank"><h5 class="card-title">Pastéis de Belém</h5></a>
                                                        <p class="card-text review"><span class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></span><a href="https://www.google.com/maps/place/Past%C3%A9is+de+Bel%C3%A9m/@38.6975105,-9.2032276,15z/data=!4m5!3m4!1s0x0:0xffeff6c6b46d9665!8m2!3d38.6975105!4d-9.2032276" class="link" target="_blank">4,6 no Google</a></p>
                                                        <p class="card-text">Grande e arejado café pastelaria com pastelaria portuguesa, inclusive tartes e pão.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="elements-options d-flex flex-row flex-md-column justify-content-start align-items-end">
                                                <button class="btn" onclick="GoUp('4')"><i class="fas fa-chevron-up"></i></button>
                                                <button class="btn" onclick="GoDown('4')"><i class="fas fa-chevron-down"></i></button>
                                                <button class="btn" onclick="RemoveElement('4')"><i class="fas fa-trash-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col buttons" id="buttons-1">
                                    <button onclick="AddParagraph('day-1')" class="btn btn-white"><i class="fas fa-plus"></i>Parágrafo</button>
                                    <button onclick="" class="btn btn-white"><i class="fas fa-plus"></i>Local</button>
                                    <button onclick="" class="btn btn-white"><i class="fas fa-plus"></i>Imagem</button>
                                </div>
                            </div>

                            <h4 class="divider" id="divider"><i class="fas fa-chevron-down" id="icon"></i><a onclick="ChangeArrow(this)" class="link" data-bs-toggle="collapse" href="#collapse-day-2" role="button" aria-expanded="false" aria-controls="collapse-day-2">Dia 2</a></h4>
                            <div class="collapse show" id="collapse-day-2">
                                <div id="day-2">
                                </div>
                                <div class="col buttons" id="buttons-2">
                                    <button onclick="AddParagraph('day-2')" class="btn btn-white"><i class="fas fa-plus"></i>Parágrafo</button>
                                    <button onclick="" class="btn btn-white"><i class="fas fa-plus"></i>Local</button>
                                    <button onclick="" class="btn btn-white"><i class="fas fa-plus"></i>Imagem</button>
                                </div>
                            </div>

                            <h4 class="divider" id="divider"><i class="fas fa-chevron-down" id="icon"></i><a onclick="ChangeArrow(this)" class="link" data-bs-toggle="collapse" href="#collapse-day-3" role="button" aria-expanded="false" aria-controls="collapse-day-3">Dia 3</a></h4>
                            <div class="collapse show" id="collapse-day-3">
                                <div id="day-3">
                                </div>
                                <div class="col buttons" id="buttons-3">
                                    <button onclick="AddParagraph('day-3')" class="btn btn-white"><i class="fas fa-plus"></i>Parágrafo</button>
                                    <button onclick="" class="btn btn-white"><i class="fas fa-plus"></i>Local</button>
                                    <button onclick="" class="btn btn-white"><i class="fas fa-plus"></i>Imagem</button>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal - local -->
    <div class="modal fade" id="addLocal" tabindex="-1" aria-labelledby="addLocal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1>Adicionar um novo local</h1>
                        <p>Escreva algo e selecione um local das sugestões apresentadas.</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <label class="label" for="title">Local</label>
                            <input type="text" placeholder="Escolha um local" name="local-box" id="local-box" onclick="RemoveError(this)" required>
                            <div class="text-center btns">
                                <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                <button id="btn-submit-local" class="btn btn-primary d-inline-flex align-items-center justify-content-center">Adicionar local</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal - imagem -->
    <div class="modal fade" id="addImage" tabindex="-1" aria-labelledby="addImage" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <h1>Adicionar uma imagem</h1>
                        <p>Selecione uma imagem para adiconar ao seu itinerário.</p>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-auto">
                            <form action="javascript:void(0);" onsubmit="DisableButton(document.getElementById('btn-submit-image'))">
                                <label class="label" for="picture">Imagem</label>
                                <input type="file" name="picture" id="picture" accept="image/*" onclick="RemoveError(this)" required>
                                <div class="text-center btns">
                                    <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                    <button type="submit" id="btn-submit-image" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="">Adiconar imagem</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <?php include SITE_DIR . '/include/footer.inc.php'; ?>
    </div>

    <?php include SITE_DIR . '/include/cookies-alert.inc.php'; ?>

    <!-- JS -->
    <script src="<?= SITE_URL ?>/js/jquery/jquery-3.5.1.min.js"></script>
    <script src="<?= SITE_URL ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= SITE_URL ?>/js/main.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= GMAPS_API_KEY ?>&libraries=places"></script>
    <script>  
        var options = {
            fields: ['name', 'url', 'photos', 'formatted_address', 'rating'],
            types: ['establishment', 'geocode'],
            componentRestrictions: {country: "pt"}
        };

        var input = document.getElementById('local-box');
        var autocomplete = new google.maps.places.Autocomplete(input, options);

        google.maps.event.addListener(autocomplete, 'place_changed', function(){
            place = autocomplete.getPlace();
        })
    </script>

    <script>
        var redirect = '<?= $id ?>/<?= $itinerary['slug'] ?>';
        var url = '<?= SITE_URL ?>';
        var contentLength = <?= $itinerary['duration'] ?>;
        <?php if (is_null($itinerary['contentJSON']) || empty($itinerary['contentJSON'])): ?>
        var initialJson = '{ "lastInsertId": "0", "content": { <?php for($i = 1; $i <= $itinerary['duration']; $i++ ): ?>"day-<?= $i ?>": []<?php if ($i != $itinerary['duration']): ?>,<?php endif; ?> <?php endfor;?> } }';
        <?php else: ?>
        var initialJson = '<?= str_replace("'", "\'", str_replace("\\", "\\\\", $itinerary['contentJSON'])) ?>';
        <?php endif; ?>
        var u = <?= $_SESSION['auth']['user']['id'] ?>;
        var i = <?= $id ?>;
    </script>
    <script src="<?= SITE_URL ?>/js/itinerary.js"></script>

    <?php include SITE_DIR . '/include/toast.inc.php'; ?>
</body>
</html>
<?php
    unset($_SESSION['errors']);
?>