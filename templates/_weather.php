<div class="weather">
    <h3><?= htmlReady($title) ?></h3>
    <img src="<?= $iconUrl.'sw-'.trim($weather->weather[0]->icon).'.svg' ?>">
    <p><?= round(($weather->main ? $weather->main->temp : $weather->temp->day) - 273.15) ?>°</p>
    <p><?= htmlReady(_($weather->weather[0]->description)) ?></p>
</div>
