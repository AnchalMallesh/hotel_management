// Function to display a welcome message
function displayWelcomeMessage() {
    alert("Welcome to Hotel Management System!");
}

// Function to toggle the visibility of the footer
function toggleFooter() {
    var footer = document.querySelector('footer');
    if (footer.style.display === 'none') {
        footer.style.display = 'block';
    } else {
        footer.style.display = 'none';
    }
}

// Event listener to trigger the welcome message when the page loads
window.onload = function() {
    displayWelcomeMessage();
}

// Event listener to toggle the visibility of the footer when the user clicks anywhere on the page
document.addEventListener('click', function(event) {
    toggleFooter();
});
