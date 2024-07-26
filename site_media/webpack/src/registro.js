const form = document.querySelector("form");
form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const action = form.action;
  const method = form.method;
  const submitBtn = form.querySelector('[type="submit"]');
  const msg_cont = document.getElementById("error_msg");
  msg_cont.innerHTML = "";
  submitBtn.disabled = true;
  const response = await fetch(action, {
    method: method,
    body: formData,
  })
    .then(function (response) {
      return response.json();
    })
    .then(function (data) {
      if (data.status == "ok") return (window.location.href = data.redirect);
      submitBtn.disabled = false;
      formError([data.msg]);
    })
    .catch(function (error) {
      formError([
        "Hubo un problema.",
        "Por favor, ponte en contacto con los administradores del sitio.",
        error.message,
      ]);
    });
});

function formError(messages) {
  const errorClases = ["c__error", "text--center", "my--5"];
  const msg_cont = document.getElementById("error_msg");
  msg_cont.innerHTML = "";

  for (const msg of messages) {
    const paragraph = document.createElement("p");
    const textNode = document.createTextNode(msg);
    paragraph.appendChild(textNode);
    paragraph.classList.add(...errorClases);
    msg_cont.appendChild(paragraph);
  }
}
