$(document).ready(function () {
  // Switch karvane ke liye save icon aur edit icon ko (using event delegation)
  $(document).on("click", ".edit-icon", function () {
    let inputField = $(this).prev("input");

    if (inputField.prop("readonly")) {
      // Enable editing
      inputField.prop("readonly", false).focus();
      $(this).removeClass("fa-pen-to-square").addClass("fa-save"); //toggleClass bhi use kr sakte h
    } else {
      // Disable editing
      inputField.prop("readonly", true);
      $(this).removeClass("fa-save").addClass("fa-pen-to-square"); //toggleClass bhi use kr sakte h

      // Send updated data to the server from AJAX
      updateField(inputField);
    }
  });

  // file input ko active krdo when clicked on the edit icon of image
  $("#imageclick").on("click", function () {
    if ($("#imageclick").hasClass("fa-pen-to-square")) {
      $("#profile-photo-upload").click();
    }
  });

  // jab koi file aajaiye to yeh function chalega
  $("#profile-photo-upload").change(function (event) {
    const file = event.target.files[0]; // Get the selected file
    if (file) {
      // Upload the file to the server
      const formData = new FormData();
      formData.append("profile_picture", file);
      // console.log(formData);

      $.ajax({
        url: "update_photo.php",
        type: "POST",
        data: formData,
        processData: false, // Required for file uploads
        contentType: false, // Required for file uploads
        success: function (response) {
          console.log("Server Response:", response);
          response = JSON.parse(response);

          if (response.status === 1) {
            $("#profile-picture").attr("src", response.filePath); // async
            //response me new image ka path aiga
            setTimeout(function () {
              alert("PROFILE IMAGE UPDATED."); //sync
            }, 500);
          } else {
            alert("ERROR WHILE UPLOADING PROFILE IMAGE.");
          }
        },
        error: function () {
          alert(
            "An error occurred while updating the profile picture. Please try again."
          );
        },
      });
    }
  });

  // Function to update qual, exp and single fields in the database
  function updateField(inputField) {
    const fieldName = inputField.attr("name").replace("[]", ""); // Remove array brackets
    let fieldValue;

    if (fieldName === "qualifications" || fieldName === "experiences") {
      // Collect all values from the respective list

      fieldValue = [];
      $(`input[name='${fieldName}[]']`).each(function () {
        //input field ka reference where name exp[] ya qual[] h
        if ($(this).val() === "") {
          //check kiya h ki null values ajax call na lagaiye
          $(this).closest("li").remove(); //removes the list item
        } else {
          fieldValue.push($(this).val());
        } // Array me sari values daldo iterate krke not the null ones
      });

      fieldValue = JSON.stringify(fieldValue); // Convert to JSON string server pe bhejne se phle
    } else if (fieldName !== "profile_picture") {
      fieldValue = inputField.val(); // Get the single value
    } else {
      return; //for image
    }
    // if (flag == 1) return;
    // Send AJAX request to update the field
    $.ajax({
      url: "update_profile.php",
      type: "POST",
      data: {
        field: fieldName,
        value: fieldValue,
      },
      success: function (response) {
        console.log("Server Response:", response, typeof response);
        response = JSON.parse(response); //string se object bana diya
        alert(response.message || "Field updated successfully!");
      },
      error: function () {
        alert("An error occurred while updating the field. Please try again.");
      },
    });
  }

  // Attaching the click event for adding qualifications when user clicks on add button
  $(".add-button.qualification").click(function (e) {
    e.preventDefault(); // Default form submission ya page refresh rokta hai.
    addQualification();
  });

  // Attaching the click event for adding experiences when user clicks on add button
  $(".add-button.experience").click(function (e) {
    e.preventDefault(); //  Default form submission ya page refresh rokta hai.
    addExperience();
  });

  // Function to add an input field for a new qualification
  function addQualification() {
    // Agar phle se input box hai to dusra mat banane do
    if ($("#new-qualification").length) {
      alert("Please enter existing field.");
      return;
    }

    $("#qualifications-list").append(`
      <li>
        <input type="text" class="inputx" id="new-qualification" name="qualifications[]" placeholder="Enter new qualification" required />
        <i class="fa-solid fa-save edit-icon"></i>
      </li>
    `);
  }

  // Function to add an input field for a new experience
  function addExperience() {
    if ($("#new-experience").length) {
      alert("Please enter existing field.");
      return;
    }

    $("#experiences-list").append(`
      <li>
        <input type="text" class="inputx" id="new-experience" name="experiences[]" placeholder="Enter new experience" required />
        <i class="fa-solid fa-save edit-icon"></i>
      </li>
    `);
  }

  // Jab user naye qualification ya experience ke save icon tab yeh fn chalega
  $(document).on("click", "#new-qualification + .edit-icon", function () {
    let inputField = $("#new-qualification");
    console.log("Input Value:", inputField.val());

    if ($.trim(inputField.val()) !== "") {
      $("#qualifications-list").append(`
        <li>
          <input type="text" class="inputx" name="qualifications[]" value="${inputField.val()}" required readonly>
          <i class="fa-solid fa-pen-to-square edit-icon"></i>
        </li>
      `);
    } else {
      alert("Can't be empty");
    }
    inputField.closest("li").remove();
  });

  // Function to save new experience
  $(document).on("click", "#new-experience + .edit-icon", function () {
    let inputField = $("#new-experience");
    console.log("Input Value:", inputField.val());

    if ($.trim(inputField.val()) !== "") {
      $("#experiences-list").append(`
        <li>
          <input type="text" class="inputx" name="experiences[]" value="${inputField.val()}" required readonly>
          <i class="fa-solid fa-pen-to-square edit-icon"></i>
        </li>
      `);
    } else {
      alert("Can't be empty.");
    }

    inputField.closest("li").remove();
  });

  //hover effect ke liye
  $(document).on("mouseenter", "li", function () {
    console.log("hover");

    $(this).find(".edit-icon").show();
  });

  $(document).on("mouseleave", "li", function () {
    console.log("hover");

    $(this).find(".edit-icon").hide();
  });

  function createbtn() {
    const btn = document.createElement("button");
    btn.className = "x";
    btn.textContent = "click me";
    $(".profile-header").append(btn);
  }

  // $(document).on("click", ".x", function () {
  //   alert("you clicked a button");
  // });
  $(".x").click(function () {
    alert("you clicked a button by click event");
  });

  createbtn();

  $(document).on("change", ".inputx", function () {
    let inputField = $(this).val();
    console.log(inputField);
    
    let newvalue = "";
    for (let i = 0; i < inputField.length; i++) {
      if (inputField[i] == " ") {
        continue;
      } else {
        newvalue += inputField[i];
      }
    }
    console.log(newvalue);
    $(this).val(newvalue);
  });
});
