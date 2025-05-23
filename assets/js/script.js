document.addEventListener('DOMContentLoaded', () => {
  const calendarContainer = document.getElementById('calendar');
  const hours = Array.from({ length: 8 }, (_, i) => `${9 + i}:00`);
  /* console.log('currentUserId'); */

  fetch('calendar.php')
    .then((response) => response.json())
    .then(({ bookedSlots, currentUserId }) => {
      console.log('currentUserId:', currentUserId);
      console.log('Booked Slots:', bookedSlots);

      generateWeeklyCalendar(bookedSlots, currentUserId);
    })
    .catch((error) => console.error('Fetch error:', error));

  function generateWeeklyCalendar(bookedSlots, currentUserId) {
    calendarContainer.innerHTML = '';

    for (let day = 0; day < 7; day++) {
      const date = new Date();
      date.setDate(date.getDate() + day);
      const dateStr = date.toISOString().split('T')[0];

      const dayDiv = document.createElement('div');
      dayDiv.classList.add('day');
      dayDiv.innerHTML = `<h3>${date.toDateString()}</h3>`;

      hours.forEach((time) => {
        const slotButton = document.createElement('button');
        slotButton.textContent = time;
        slotButton.setAttribute('data-date', dateStr);
        slotButton.setAttribute('data-time', time);

        slotButton.classList.remove('available', 'booked', 'booked-by-user');

        const booking = bookedSlots.find(
          (slot) => slot.date === dateStr && slot.time === time
        );

        if (booking) {
          if (booking.user_id === currentUserId) {
            slotButton.classList.add('booked-by-user');
            slotButton.onclick = () => cancelBooking(dateStr, time, slotButton);
          } else {
            slotButton.classList.add('booked');
            slotButton.disabled = true;
          }
        } else {
          slotButton.classList.add('available');
          slotButton.onclick = () => bookSlot(dateStr, time, slotButton);
        }

        dayDiv.appendChild(slotButton);
      });

      calendarContainer.appendChild(dayDiv);
    }
  }

  function bookSlot(date, time, button) {
    fetch('book.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ date, time }),
    })
      .then((response) => response.json())
      .then((result) => {
        alert(result.message);
        if (result.success) {
          button.classList.remove('available');
          button.classList.add('booked-by-user');
          button.onclick = null;
          button.onclick = () => cancelBooking(date, time, button);
        }
      });
  }

  function cancelBooking(date, time, button) {
    const confirmCancel = confirm(
      'Are you sure you want to cancel this booking?'
    );

    if (!confirmCancel) {
      return;
    }

    fetch('cancel_booking.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ date, time }),
    })
      .then((response) => response.json())
      .then((result) => {
        alert(result.message);
        if (result.success) {
          button.classList.remove('');
          button.classList.add('available');
          button.onclick = null;
          button.onclick = () => bookSlot(date, time, button);
        }
      });
  }
});
