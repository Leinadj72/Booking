document.addEventListener('DOMContentLoaded', () => {
  const calendarContainer = document.getElementById('calendar');
  const hours = Array.from({ length: 8 }, (_, i) => `${9 + i}:00`);

  fetch('fetch_bookings.php')
    .then((response) => response.json())
    .then((bookedSlots) => {
      generateWeeklyCalendar(bookedSlots);
      generateMonthlyCalendar(bookedSlots);
    });

  function generateWeeklyCalendar(bookedSlots) {
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

        if (
          bookedSlots.some(
            (slot) => slot.date === dateStr && slot.time === time
          )
        ) {
          slotButton.disabled = true;
          slotButton.classList.add('booked');
        }

        slotButton.addEventListener('click', () => {
          bookSlot(dateStr, time);
        });

        dayDiv.appendChild(slotButton);
      });

      calendarContainer.appendChild(dayDiv);
    }
  }

  function generateMonthlyCalendar(bookedSlots) {
    let today = new Date();
    let currentYear = today.getFullYear();
    let currentMonth = today.getMonth();
    let daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

    for (let day = 1; day <= daysInMonth; day++) {
      let dateString = `${currentYear}-${String(currentMonth + 1).padStart(
        2,
        '0'
      )}-${String(day).padStart(2, '0')}`;
      let dayDiv = document.createElement('div');
      dayDiv.classList.add('calendar-day');
      dayDiv.textContent = day;

      if (bookedSlots.some((slot) => slot.date === dateString)) {
        dayDiv.classList.add('booked');
        dayDiv.innerHTML = `${day} 🔴`;
      } else {
        dayDiv.classList.add('available');
        dayDiv.innerHTML = `${day} ✅`;
        dayDiv.addEventListener('click', () => bookSlot(dateString));
      }

      calendarContainer.appendChild(dayDiv);
    }
  }

  function bookSlot(date, time = null) {
    let bookingData = { date };
    if (time) {
      bookingData.time = time;
    }

    fetch('book.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(bookingData),
    })
      .then((response) => response.json())
      .then((result) => {
        alert(result.message);
        if (result.success) {
          location.reload();
        }
      });
  }
});
