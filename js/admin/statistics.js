$(document).ready(function () {
  if ($('#date_from').length > 0) {
    var picker = new Lightpick({
      field: document.getElementById('date_from'),
      secondField: document.getElementById('date_to'),
      singleDate: false,
      format: 'YYYY-MM-DD',
      onSelect: function (start, end) { }
    });
  }

  $('#report_generate').on('click', function () {
    $.ajax({
      type: 'POST',
      data: {
        'action': 'generate_report',
        'date_from': $('#date_from').val(),
        'date_to': $('#date_to').val(),
        'stat_type': $('#stat_tryb').val(),
        'producer_id': $('#producer_id').val()
      },
      url:"/admin/statystyki_sprzedazy.php",
      beforeSend: function () {

      },
      success: function (data) {
        body = jQuery.parseJSON(data);
        doc = new jspdf.jsPDF();
        var title = 'Raport sprzedazy od ' + $('#date_from').val() + ' do ' + $('#date_to').val();
        doc.text(title, 14, 10);
        doc.autoTable({
          head: [
            { lp: 'Lp', name: 'Produkt', code: 'Kod produktu', category: 'Kategoria', quantity: 'Ilosc', sum: 'Suma' }
          ],
          body: body,
          //startY: doc.lastAutoTable.finalY + 14,
          theme: 'grid',
        });
        doc.setFont('helvetica');
        doc.setFontSize(10);
        doc.setCharSpace(0);
        doc.save('Raport_' + $('#date_from').val() + '_' + $('#date_to').val() +  '.pdf');
      }
    });
  })
});