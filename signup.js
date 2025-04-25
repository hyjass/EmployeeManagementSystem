$(document).ready(function () {
  // IMAGE KO DISPLAY
  $("#profilePicture").change(function (event) {
    const file = event.target.files[0]; // file jo select hui h usko select kiya h
    // console.log(event);
    if (file) {
      $("#profile-img").attr("src", URL.createObjectURL(file)); // Show image
    }
  });

  // Add Qualification
  $("#addQualification").click(function () {
    let count = $("#qualifications .qualification-item").length + 1; // Get count dynamically
    $("#qualifications").append(`
      <div class="qualification-item">
        <div id="text">Qualification ${count}</div>
        <input type="text" name="qualifications[]" required> 
        <button type="button" class="remove-button">Remove</button>
      </div>
    `);
  });

  // Add Experience
  $("#addExperience").click(function () {
    let count = $("#experiences .experience-item").length + 1; // Get count dynamically
    $("#experiences").append(`
      <div class="experience-item">
        <div id="text">Experience ${count}</div>
        <input type="text" name="experiences[]" required> 
        <button type="button" class="remove-button">Remove</button>
      </div>
    `);
  });

  // Remove Qualification or Experience ,yaha pe alag type ka use hua  due to dynamic 
  $(document).on("click", ".remove-button", function () {
    $(this).closest(".qualification-item, .experience-item").remove();
    updateNumbers();
  });

  // Function to update numbering after removal

  function updateNumbers() {
    $("#qualifications .qualification-item").each(function (index) {
      $(this)
        .find("#text")
        .text(`Qualification ${index + 1}`);
    });
    
    $("#experiences .experience-item").each(function (index) {
      $(this)
        .find("#text")
        .text(`Experience ${index + 1}`);
    });
  }
});
