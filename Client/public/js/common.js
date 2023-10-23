// 分頁
function navTo(page) {
    window.location.href = '/' + page;
}


// Toast function
function showSuccessToast(title) {
    toast({
        title: title,
        type: "success",
    });
}

function showErrorToast(title) {
    toast({
        title: title,
        type: "error",
    });
}

function toast({
    title = "",
    type = "",
    duration = 2000
}) {
    const main = document.getElementById("toast");
    if (main) {
        const toast = document.createElement("div");
        // Auto remove toast
        const autoRemoveId = setTimeout(function() {
            main.removeChild(toast);
        }, duration + 1000);
        // Remove toast when clicked
        toast.onclick = function(e) {
            if (e.target.closest(".toast__close")) {
                main.removeChild(toast);
                clearTimeout(autoRemoveId);
            }
        };

        const icons = {
            success: "fas fa-check-circle",
            info: "fas fa-info-circle",
            warning: "fas fa-exclamation-circle",
            error: "fas fa-exclamation-circle"
        };
        const icon = icons[type];
        const delay = (duration / 1000).toFixed(2);

        toast.classList.add("toast", "text-center", "mt-3");
        toast.style.animation = `slideInLeft ease .3s, fadeOut linear 1s ${delay}s forwards`;
        toast.style.width = `20rem;`;
        toast.innerHTML = `<h3 class="toastType ${type}">${title}</h3>`;
        main.appendChild(toast);
    }
}