// home/js/dashboard.js
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.widget').forEach(function(w) {
    w.addEventListener('click', function () {
      var panel = w.getAttribute('data-panel');
      openPanel(panel);
    });
  });

  var modal = document.getElementById('dashModal');
  var closeBtn = document.getElementById('closeModal');
  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (modal) modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });

  window.openPanel = function(name) {
    var modalInner = document.getElementById('modalContent');
    if (!modalInner) return;
    var data = window.DASH_DATA || {};

    if (name === 'profile' && data.user) {
      modalInner.innerHTML = '<h2>Profile</h2>' +
        '<p><strong>Name:</strong> ' + escapeHtml(data.user.firstName + ' ' + data.user.surname) + '</p>' +
        '<p><strong>Email:</strong> ' + escapeHtml(data.user.email) + '</p>' +
        '<p><strong>Phone:</strong> ' + escapeHtml(data.user.numberPhone) + '</p>';
    } else if (name === 'recommendations') {
      var html = '<h2>Recommendations</h2>';
      (data.recs || []).forEach(function(r){
        html += '<p><strong>' + escapeHtml(r.title) + '</strong> — ' + escapeHtml(r.summary) + '</p>';
      });
      modalInner.innerHTML = html;
    } else if (name === 'classes') {
      var html = '<h2>Your Classes</h2>';
      var cls = data.classes || [];
      if (cls.length === 0) html += '<p>No classes</p>';
      else {
        cls.forEach(function(c){
          html += '<p><strong>' + escapeHtml(c.title) + '</strong> — ' + escapeHtml(c.day_of_week) + ' ' + escapeHtml(c.start_time) + '</p>';
        });
      }
      modalInner.innerHTML = html;
    } else if (name === 'calendar') {
      var html = '<h2>Calendar (upcoming)</h2>';
      var ev = data.events || [];
      if (ev.length === 0) html += '<p>No upcoming events.</p>';
      else {
        html += '<div class="calendar-grid">';
        ev.forEach(function(e){
          var isToday = (new Date()).toDateString() === new Date(e.start_time || e.start_time).toDateString();
          html += '<div class="calendar-day' + (isToday ? ' today' : '') + '"><strong>' + escapeHtml(e.title) + '</strong><br />' + escapeHtml(e.day_of_week || '') + ' ' + escapeHtml(e.start_time || '') + '</div>';
        });
        html += '</div>';
      }
      modalInner.innerHTML = html;
    } else if (name === 'tips') {
      modalInner.innerHTML = '<h2>Health Tips</h2><ul><li>Drink 2-3L water daily</li><li>Sleep 7-9 hours</li><li>Mobility work daily</li></ul>';
    } else if (name === 'plan') {
      var p = data.plan;
      if (p) modalInner.innerHTML = '<h2>' + escapeHtml(p.title) + '</h2><p>' + escapeHtml(p.description || '') + '</p><p>Expires: ' + escapeHtml(p.expires_at || 'N/A') + '</p>';
      else modalInner.innerHTML = '<p>No active plan</p>';
    } else {
      modalInner.innerHTML = '<p>No content</p>';
    }

    if (modal) { modal.style.display = 'flex'; modal.setAttribute('aria-hidden','false'); }
  };

  function closeModal() {
    var modal = document.getElementById('dashModal');
    if (modal) { modal.style.display = 'none'; modal.setAttribute('aria-hidden','true'); }
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"'\/]/g, function (s) {
      var map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;'};
      return map[s];
    });
  }
});
