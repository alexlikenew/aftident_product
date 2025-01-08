$(document).ready(function() {
  let distributors = []
  let distributorsFiltered = []
  let markersList = []
  let listElement = $('#distributors-list')

  function createListItem(item) {
    return $(`
    <div class="distributors-list-item">
        <div class="title">${item.title}</div>
        <div class="row"><strong>Ulica:</strong> ${item.street} ${item.building_no}</div>
        <div class="row"><strong>Telefon:</strong> ${item.phone}</div>
        <div class="row"><strong>Email:</strong> ${item.email}</div>
        <a href="/dystrybutorzy/${item.title_url}" class="link">
            Czytaj więcej
            <img src="/templates/default/img/icons/icon-more-white.svg" width="15" height="15" alt="${item.title}">
        </a>
    </div>
  `);
  }

  const attribution = new ol.control.Attribution({
    collapsible: false
  });

  let map = new ol.Map({
    controls: ol.control.defaults({attribution: false}),
    layers: [
      new ol.layer.Tile({
        source: new ol.source.OSM({
          url: 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png',
          maxZoom: 18,
          crossOrigin: null
        })
      })
    ],
    target: 'map',
    view: new ol.View({
      center: ol.proj.fromLonLat([19.314, 51.965]),
      maxZoom: 18,
      zoom: 6.5
    })
  });

  const icon = TPL_URL+'/img/icons/icon-map-pin.png';
  var vectorSource = new ol.source.Vector({
    features: markersList
  });
  var vectorLayer = new ol.layer.Vector({
    source: vectorSource,
    style: new ol.style.Style({
      image: new ol.style.Icon({
        anchor: [0.5, 1],
        src: icon
      })
    })
  });

  map.addLayer(vectorLayer);

  const element = document.getElementById('popup');
  const content = document.getElementById('popup-content');
  const closer = document.getElementById('popup-close');
  const page = $('#page_no').val();

  function getAll () {
    $.ajax({
      type: 'POST',
      data: {
        'getAll': true,
        'ajax': true,
      },
      url: BASE_URL + "/dystrybutorzy",
      beforeSend: function () { },
      success: function (data) {
        const json = JSON.parse(data)
        distributors = json.data
        markersList = []

        vectorSource.clear();
        distributors.forEach(function (item) {
          createMarker(item, map)
        })

        vectorSource.addFeatures(markersList);
      }
    });
  }

  function getByPage () {
    $.ajax({
      type: 'POST',
      data: {
        'getAll': true,
        'ajax': true,
        'page': page
      },
      url: BASE_URL + "/dystrybutorzy",
      beforeSend: function () { },
      success: function (data) {
        const json = JSON.parse(data)
        distributors = json.data
        listElement.html('')

        distributors.forEach(function (item) {
          listElement.append(createListItem(item))
        })
      }
    });
  }

  function getByPostCode (postcode) {
    $.ajax({
      type: 'POST',
      data: {
        'postcode': postcode,
        'ajax': true
      },
      url: BASE_URL + "/dystrybutorzy",
      beforeSend: function () { },
      success: function (data) {
        const json = JSON.parse(data)
        distributors = json.data
        markersList = []
        listElement.html('')

        vectorSource.clear();
        distributors.forEach(function (item) {
          listElement.append(createListItem(item))
          createMarker(item, map)
        })

        vectorSource.addFeatures(markersList);
      }
    });
  }

  function createMarker(item, map) {

    let marker = new ol.Feature({
      geometry: new ol.geom.Point(ol.proj.fromLonLat([Number(item.longitude), Number(item.latitude)])),
      data: item
    });

    markersList.push(marker)

    const overlay = new ol.Overlay({
      element: element,
      positioning: 'bottom-center',
      stopEvent: false
    });
    map.addOverlay(overlay);

    closer.onclick = function () {
      overlay.setPosition(undefined);
      closer.blur();
      return false;
    };

    map.on('click', function(evt) {
      var feature = map.forEachFeatureAtPixel(evt.pixel,
        function(feature, layer) {
          return feature;
        });

      let data
      if (feature) data = feature.values_.data

      if (feature) {
        overlay.setPosition(evt.coordinate);
        element.classList.add('open')
        content.innerHTML = `
          <div class="title">${data.city}</div>
          <div class="address">
            ${data.title} ${data.postcode} ${data.city}
          </div>
          <div class="row">
            <strong>Ulica:</strong><br>
            ${data.street} ${data.building_no}
          </div>
          <div class="row">
            <strong>Telefon:</strong><br>
            ${data.phone}
          </div>
          <div class="row">
            <strong>Email:</strong><br>
            ${data.email}
          </div>
        `

      } else {
        element.classList.add('open')
      }
    });
  }

  $('#map_by_postcode').click(function(){
    let postcode = $('#postcode').val();

    if (postcode.length > 0) {
      getByPostCode(postcode)
      $('.pagination').hide()
    } else {
      getAll()
      getByPage()
      $('.pagination').show()
    }

    return false;
  });

  getAll()
  getByPage()
});