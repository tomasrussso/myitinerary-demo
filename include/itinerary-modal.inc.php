<?php if(isset($_SESSION['auth'])): ?>
<?php
$url = substr($_SERVER['REQUEST_URI'], 1);
$urlArray = explode('myitinerary.pt/', $url);
array_shift($urlArray);
$redirect = implode('/', $urlArray);
?>  
<!-- Modal - Criar itinerário -->
<div class="modal fade" id="createItinerary" tabindex="-1" aria-labelledby="createItinerary" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row text-center">
                    <h1>Criar um novo itinerário</h1>
                    <p>Antes de começar, defina algumas informações básicas sobre o seu itinerário.</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <form action="<?= SITE_URL ?>/source/itinerary/create.php" class="form" method="post" autocomplete="off" onsubmit="DisableButton(document.getElementById('btn-submit'))">
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                            <?php csrf('create-itinerary'); ?>
                            <label for="title">Título</label> <br>
                            <input <?php if (isset($_SESSION['errors']['title'])): ?> class="input-error" <?php endif; ?> type="text" id="itin-title" name="title" placeholder="Dê um nome ao seu itinerário" <?php if (isset($_SESSION['title'])): ?>value="<?= $_SESSION['title'] ?>"<?php endif; ?> onclick="RemoveError(this)" required> <br>
                            <?php if (isset($_SESSION['errors']['title'])): ?>
                                <p class="error mb-3" style="margin-top: -8px"><?= $_SESSION['errors']['title'] ?></p>
                            <?php endif; ?>
                            <label for="duration">Duração da viagem</label> <br>
                            <input <?php if (isset($_SESSION['errors']['duration'])): ?> class="input-error" <?php endif; ?> type="number" id="duration" name="duration" placeholder="Máximo de 30 dias" <?php if (isset($_SESSION['duration'])): ?>value="<?= $_SESSION['duration'] ?>"<?php endif; ?> onclick="RemoveError(this)" required> <br>
                            <?php if (isset($_SESSION['errors']['duration'])): ?>
                                <p class="error mb-3" style="margin-top: -8px"><?= $_SESSION['errors']['duration'] ?></p>
                            <?php endif; ?> 
                            <label for="locations">Cidades</label> <br>
                            <select class="ui fluid search dropdown" multiple id="locations" name="locations[]"> 
                                <option value="">Escolha até 8 cidades</option>
                                <?php 
                                    $cities = $db->query('SELECT * FROM city ORDER BY name')->fetchAll();
                                    foreach ($cities as $city):
                                ?>
                                <option value="<?= $city['id'] ?>" <?php if (isset($_SESSION['locations']) && in_array($city['id'], $_SESSION['locations'])): ?>selected="selected"<?php endif; ?>><?= $city['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($_SESSION['errors']['locations'])): ?>
                                <p class="error" style="margin-top: -8px"><?= $_SESSION['errors']['locations'] ?></p>
                            <?php endif; ?>
                            <div class="text-center btns" style="margin-top: 38px;">
                                <button type="button" class="btn btn-white cancel" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                <button id="btn-submit" type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" onclick="SetLoading(this, document.getElementById('itin-title'), document.getElementById('duration'))">Começar a criar!</button>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/dropdown.min.js" integrity="sha512-8F/2JIwyPohlMVdqCmXt6A6YQ9X7MK1jHlwBJv2YeZndPs021083S2Z/mu7WZ5g0iTlGDYqelA9gQXGh1F0tUw==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/components/transition.min.js" integrity="sha512-MCuLP92THkMwq8xkT2cQg5YpF30l3qzJuBRf/KsbQP1czFkRYkr2dSkCHmdJETqVmvIq5Y4AOVE//Su+cH+8QA==" crossorigin="anonymous"></script>
<script>
    $('.ui.dropdown').dropdown({
        maxSelections: 8,
        message: {
            count: '{count} selecionados',
            maxSelections: 'Apenas pode adicionar {maxCount} cidades',
            noResults: 'Sem resultados'
        },
        direction: 'upward'
    });

    $( "#createItinerary" ).on('shown.bs.modal', function(){
        width = document.getElementById('duration').offsetWidth;
        document.querySelector('div.ui.dropdown').setAttribute("style", "width: " + width + "px");
    });    

    var windowWidth = $(window).width();
    $(window).on('resize', function() {
        if ($(this).width() !== windowWidth) {
            width = document.getElementById('duration').offsetWidth;
            document.querySelector('div.ui.dropdown').setAttribute("style", "width: " + width + "px");
        }
    });
</script>
<?php if (isset($_SESSION['errors']['modal'])): ?>
<script>
    $(document).ready(function(){
        $("#<?= $_SESSION['errors']['modal'] ?>").modal('show');
    });
</script>
<?php endif; ?>
<?php endif; ?>
<?php
    unset($_SESSION['width']); 
    unset($_SESSION['title']); 
    unset($_SESSION['duration']);
    unset($_SESSION['locations']);
?>