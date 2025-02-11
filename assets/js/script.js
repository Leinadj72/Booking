document.addEventListener('DOMContentLoaded', () => {
  const calendarContainer = document.getElementById('calendar');
  const hours = Array.from({ length: 8 }, (_, i) => `${9 + i}:00`);

  fetch('fetch_bookings.php')
    .then((response) => response.json())
    .then(({ bookedSlots, currentUserId }) =>
      generateWeeklyCalendar(bookedSlots, currentUserId)
    );

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

        const booking = bookedSlots.find(
          (slot) => slot.date === dateStr && slot.time === time
        );

        if (booking) {
          if (booking.user_id === currentUserId) {
            slotButton.classList.add('booked-by-user');
            slotButton.addEventListener('click', () =>
              cancelBooking(dateStr, time, slotButton)
            );
          } else {
            slotButton.classList.add('booked');
            slotButton.disabled = true;
          }
        } else {
          slotButton.classList.add('available');
          slotButton.addEventListener('click', () =>
            bookSlot(dateStr, time, slotButton)
          );
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
          button.removeEventListener('click', () =>
            bookSlot(date, time, button)
          );
          button.addEventListener('click', () =>
            cancelBooking(date, time, button)
          );
        }
      });
  }

  function cancelBooking(date, time, button) {
    fetch('cancel_booking.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ date, time }),
    })
      .then((response) => response.json())
      .then((result) => {
        alert(result.message);
        if (result.success) {
          button.classList.remove('booked-by-user');
          button.classList.add('available');
          button.removeEventListener('click', () =>
            cancelBooking(date, time, button)
          );
          button.addEventListener('click', () => bookSlot(date, time, button));
        }
      });
  }
});
