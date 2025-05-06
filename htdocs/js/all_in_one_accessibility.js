// Default widget Settings
const defaultSettings = {
  widget_position: "bottom_right",
  widget_color_code: "#000000",
  widget_icon_type: "default",
  widget_icon_size: "medium",
  widget_size: "standard",
  widget_icon_size_custom: "",
  widget_position_top: 0,
  widget_position_bottom: 0,
  widget_position_left: 0,
  widget_position_right: 0,
  platform_widget_status: 0
};

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('domain-list').value = "";
  updateVisibility();
});
// Hide or show form settings  based on whether a domain is selected
function updateVisibility() {
  const selectedValue = document.getElementById('domain-list').value;
  const elementsToHide = [
    '.custom_position_div',
    '.mb-3.row.widget-position',
    '#position_html',
    '#colorcode_html',
    '.text-dark',
    '.form-radios.mb-3',
    '#select_icon_type',
    '#select_icon_size',
    '.save-changes-btn',
    '.platform_widget_status'
  ];

  elementsToHide.forEach(selector => {
    document.querySelectorAll(selector).forEach(el => {
      el.style.display = selectedValue === "" ? 'none' : '';
    });
  });
}
// Handle domain selection change and fetch data
function handleDomainChange() {
  const domainSelect = document.getElementById('domain-list');
  const domainName = domainSelect.selectedOptions[0].text;

  if (!domainName || domainName === "Select Domain") {

    return;// Do nothing if no domain is selected
  }

  fetchApiResponse(domainName);
}
// Enable/disable widget script for the selected domain
function toggleWidgetScript(switcher) {
  const isChecked = switcher.checked ? 1 : 0;
  const domainId = document.getElementById('domain-list').value;
  // if domain is blank selected then widget hide/show switcher is false
  if (domainId === '') {
    switcher.checked = false;
    return;
  }
  //To send the domain ID and widget checkbox status to update_widget_script.php immediately when a domain is selected,
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "update_widget_script.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("enable_widget=" + isChecked + "&domain_id=" + domainId);
}
// Fetch widget settings from ADA dashboard widget-settings API
function fetchApiResponse(domainName) {
  const apiUrl = "https://ada.skynettechnologies.us/api/widget-settings";

  fetch(apiUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/json" // Specify the content type
    },
    body: JSON.stringify({ website_url: domainName }) // Pass the domain name in the request body
  })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json(); // Parse the JSON response
    })
    .then((result) => {
      // Check if result and result.Data are valid
      if (result && result.Data && Object.keys(result.Data).length > 0) {
        document.getElementById('widget-container').style.display = 'block';
        document.getElementById('upgrade_html_notes').style.display = 'none';
        // Prepare widget settings from API or use defaults
        const settings = {
          widget_position: result.Data.widget_position || defaultSettings.widget_position,
          widget_color_code: result.Data.widget_color_code || defaultSettings.widget_color_code,
          widget_icon_type: result.Data.widget_icon_type || defaultSettings.widget_icon_type,
          widget_icon_size: result.Data.widget_icon_size || defaultSettings.widget_icon_size,
          widget_size: result.Data.widget_size || defaultSettings.widget_size,
          widget_icon_size_custom: result.Data.widget_icon_size_custom || defaultSettings.widget_icon_size_custom,
          widget_position_top: result.Data.widget_position_top || 0,
          widget_position_bottom: result.Data.widget_position_bottom || 0,
          widget_position_left: result.Data.widget_position_left || 0,
          widget_position_right: result.Data.widget_position_right || 0,
          platform_widget_status: result.Data.platform_widget_status || defaultSettings.platform_widget_status,
        };
        // send settings on populateSettings function
        populateSettings(settings);

      } else {
        alert("The selected domain does not have an active purchased plan subscription.");
        // hide the settings form if the domain is not register
        document.getElementById('widget-container').style.display = 'none';
        document.getElementById('upgrade_html_notes').style.display = 'block';

      }
    })
    .catch(error => {

      // Handle error scenarios like invalid response or network issues
    });
}
// Show or hide widget configuration options when checkbox is toggled
function toggleWidgetVisibility(checkbox) {
  const elementsToToggle = [
    '.custom_position_div',
    '.mb-3.row.widget-position',
    '#position_html',
    '#colorcode_html',
    '.text-dark',
    '.form-radios.mb-3',
    '#select_icon_type',
    '#select_icon_size',
  ];
  const isChecked = checkbox.checked;

  elementsToToggle.forEach(selector => {
    document.querySelectorAll(selector).forEach(el => {
      el.style.display = isChecked ? '' : 'none';
    });
  });
}
// Populate form fields with settings
function populateSettings(settings) {

  const checkbox = document.getElementById('platform_widget_status');
  checkbox.checked = settings.platform_widget_status === 1; // Set the checkbox based on the API response
  toggleWidgetVisibility(checkbox);

  toggleWidgetScript(checkbox);
  // fetch and set color code 
  const colorField = document.getElementById("colorcode");
  if (colorField) {
    colorField.value = settings.widget_color_code;
  }
  const typeOptions = document.querySelectorAll('input[name="aioa_icon_type"]');
  typeOptions.forEach((option) => {
    if (option.value === settings.widget_icon_type) {
      option.checked = true;
    }
  });

  const sizeOptions = document.querySelectorAll('input[name="aioa_icon_size"]');
  sizeOptions.forEach((option) => {
    if (option.value === settings.widget_icon_size) {
      option.checked = true;
    }
  });

  const iconImg = `https://www.skynettechnologies.com/sites/default/files/${settings.widget_icon_type}.svg`;
  $(".icon-img").attr("src", iconImg);

  const widget_icon_size_custom = document.getElementById("widget_icon_size_custom");
  if (widget_icon_size_custom) {
    widget_icon_size_custom.value = settings.widget_icon_size_custom;
  }
  const positionRadio = document.querySelector(`input[name="position"][value="${settings.widget_position}"]`);
  if (positionRadio) {
    positionRadio.checked = true;
  }
  const widget_size = document.querySelector(`input[name="widget_size"][value="${settings.widget_size}"]`);
  if (widget_size) {
    widget_size.checked = true;
  }

  // Handle setting custom widget position
  const customPositionXField = document.getElementById("custom_position_x_value");

  const xDirectionSelect = document.querySelector(".custom-position-controls select:nth-child(1)");

  if (customPositionXField && xDirectionSelect) {
    if (settings.widget_position_right > 0) {
      customPositionXField.value = settings.widget_position_right;
      xDirectionSelect.value = "cust-pos-to-the-right";
    } else if (settings.widget_position_left > 0) {
      customPositionXField.value = settings.widget_position_left;
      xDirectionSelect.value = "cust-pos-to-the-left";
    } else {
      customPositionXField.value = 0;
      xDirectionSelect.value = "cust-pos-to-the-right"; // Default direction
    }
  }

  // Handle setting custom widget position (Y-axis)
  const customPositionYField = document.getElementById("custom_position_y_value");
  const yDirectionSelect = document.querySelector(".custom-position-controls select:nth-child(2)");

  if (customPositionYField && yDirectionSelect) {
    if (settings.widget_position_bottom > 0) {
      customPositionYField.value = settings.widget_position_bottom;
      yDirectionSelect.value = "cust-pos-to-the-lower";
    } else if (settings.widget_position_top > 0) {
      customPositionYField.value = settings.widget_position_top;
      yDirectionSelect.value = "cust-pos-to-the-upper";
    } else {
      customPositionYField.value = 0;
      yDirectionSelect.value = "cust-pos-to-the-lower"; // Default direction
    }
  }



}




// On window load, call domain change handler and fetch settings
window.onload = function () {
  handleDomainChange();
  const domainSelect = document.getElementById('domain-list');
  const domainName = domainSelect.selectedOptions[0].text;

  if (!domainName || domainName === "Select Domain") {

    return;
  }

  fetchApiResponse(domainName);

};


document.addEventListener("DOMContentLoaded", function () {

  // Custom Switchers
  var positionSwitcher = document.getElementById("custom-position-switcher");
  if (positionSwitcher) {
    positionSwitcher.addEventListener("click", function () {
      document.querySelectorAll(".custom-position-controls").forEach(function (el) {
        el.classList.toggle("hide");
      });
      document.querySelectorAll(".widget-position").forEach(function (el) {
        el.classList.toggle("hide");
      });
    });
  }

  var sizeSwitcher = document.getElementById("custom-size-switcher");
  if (sizeSwitcher) {
    sizeSwitcher.addEventListener("click", function () {
      document.querySelectorAll(".custom-size-controls").forEach(function (el) {
        el.classList.toggle("hide");
      });
      document.querySelectorAll(".widget-icon").forEach(function (el) {
        el.classList.toggle("hide");
      });
    });
  }

});

const sizeOptions = document.querySelectorAll('input[name="aioa_icon_size"]');
const sizeOptionsImg = document.querySelectorAll('input[name="aioa_icon_size"] + label img');
const typeOptions = document.querySelectorAll('input[name="aioa_icon_type"]');
const positionOptions = document.querySelectorAll('input[name="position"]');
const custSizePreview = document.querySelector(".custom-size-preview img");
const custSizePreviewLabel = document.querySelector(".custom-size-preview .value span");

// Set default value to custom position inputs
var positions = {
  top_left: [20, 20],
  middel_left: [20, 50],
  bottom_center: [50, 20],
  top_center: [50, 20],
  middel_right: [20, 50],
  bottom_right: [20, 20],
  top_right: [20, 20],
  bottom_left: [20, 20],
};
// Set default values for custom position fields based on selected radio
positionOptions.forEach((option) => {
  var ico_position = document.querySelector('input[name="position"]:checked').value;
  document.getElementById("custom_position_x_value").value = positions[ico_position][0];
  document.getElementById("custom_position_y_value").value = positions[ico_position][1];
  option.addEventListener("click", (event) => {
    var ico_position = document.querySelector('input[name="position"]:checked').value;
    document.getElementById("custom_position_x_value").value = positions[ico_position][0];
    document.getElementById("custom_position_y_value").value = positions[ico_position][1];
  });
});

// Set icon on type select
typeOptions.forEach((option) => {
  option.addEventListener("click", (event) => {
    var ico_type = document.querySelector('input[name="aioa_icon_type"]:checked').value;
    sizeOptionsImg.forEach((option2) => {
      option2.setAttribute("src", "https://www.skynettechnologies.com/sites/default/files/" + ico_type + ".svg");
    });
    custSizePreview.setAttribute("src", "https://www.skynettechnologies.com/sites/default/files/" + ico_type + ".svg");
  });
});

// Set icon on size select
sizeOptions.forEach((option) => {
  var ico_size_value = document
    .querySelector('input[name="aioa_icon_size"]:checked + label img')
    .getAttribute("width");
  // Set default value to custom size input
  document.getElementById("widget_icon_size_custom").value = widget_icon_size_custom;
  custSizePreviewLabel.innerHTML = ico_size_value;
  option.addEventListener("click", (event) => {
    var ico_width = document
      .querySelector('input[name="aioa_icon_size"]:checked + label img')
      .getAttribute("width");
    var ico_height = document
      .querySelector('input[name="aioa_icon_size"]:checked + label img')
      .getAttribute("height");
    custSizePreview.setAttribute("width", ico_width);
    custSizePreview.setAttribute("height", ico_height);
    document.getElementById("widget_icon_size_custom").value = ico_width;
    custSizePreviewLabel.innerHTML = ico_width;
  });
});

const customSizeInput = document.getElementById("widget_icon_size_custom");
// Set icons size on input change
if (customSizeInput) {
  customSizeInput.addEventListener("input", function () {
    var ico_size_value = document.getElementById("widget_icon_size_custom").value;
    if (ico_size_value >= 20 && ico_size_value <= 150) {
      custSizePreview.setAttribute("width", ico_size_value);
      custSizePreview.setAttribute("height", ico_size_value);
      custSizePreviewLabel.innerHTML = ico_size_value;
    }
    if (ico_size_value < 20) {
      custSizePreview.setAttribute("width", 20);
      custSizePreview.setAttribute("height", 20);
      custSizePreviewLabel.innerHTML = 20;
    }
    if (ico_size_value > 150) {
      custSizePreview.setAttribute("width", 150);
      custSizePreview.setAttribute("height", 150);
      custSizePreviewLabel.innerHTML = 150;
    }
  });
}

// Get initial domain name from dropdown
const domainList = document.getElementById('domain-list');
let server_name = '';

if (domainList && domainList.options.length > 0) {
  server_name = domainList.options[0].text;
} else {

}



// Function to update the server name on dropdown change
function updateServerName() {
  server_name = document.getElementById('domain-list').selectedOptions[0].text;


}

// when submit button is click call this function
function f1() {

  const checkboxe = document.getElementById('platform_widget_status');

  // Store the value based on whether the checkbox is checked or not
  const platform_widget_status = checkboxe.checked ? 1 : 0;


  const checkbox1 = document.getElementById('platform_widget_status');
  // Set the checkbox based on the API response

  // Trigger the script toggle based on initial state
  toggleWidgetScript(checkbox1);








  // Update settings
  var colorcode = $("#colorcode").val();
  var icon_position = document.querySelector('input[name="position"]:checked').value;
  var icon_type = document.querySelector('input[name="aioa_icon_type"]:checked').value;
  var icon_size = document.querySelector('input[name="aioa_icon_size"]:checked').value;
  var widget_size = document.querySelector('input[name="widget_size"]:checked').value;
  var widget_icon_size_custom = $("#widget_icon_size_custom").val();

  const custom_position_x = $("#custom_position_x_value").val() || 0; // Default to 0 if no value
  const custom_position_y = $("#custom_position_y_value").val() || 0;
  const x_position_direction = $(".custom-position-controls select").eq(0).val();
  const y_position_direction = $(".custom-position-controls select").eq(1).val();

  // Initialize widget position values
  let widget_position_right = null;
  let widget_position_left = null;
  let widget_position_top = null;
  let widget_position_bottom = null;

  // Update widget position based on the selected directions
  if (x_position_direction === "cust-pos-to-the-right") {
    widget_position_right = custom_position_x;
  } else if (x_position_direction === "cust-pos-to-the-left") {
    widget_position_left = custom_position_x;
  }

  if (y_position_direction === "cust-pos-to-the-lower") {
    widget_position_bottom = custom_position_y;
  } else if (y_position_direction === "cust-pos-to-the-upper") {
    widget_position_top = custom_position_y;
  }






  const is_widget_custom_sizecheckbox = document.getElementById('custom-position-switcher');

  // Store the value based on whether the checkbox is checked or not
  const is_widget_custom_size = is_widget_custom_sizecheckbox.checked ? 1 : 0;

  // Perform any action with the value

  const is_widget_custom_positioncheckbox = document.getElementById('custom-position-switcher');

  // Store the value based on whether the checkbox is checked or not
  const is_widget_custom_position = is_widget_custom_positioncheckbox.checked ? 1 : 0;

  // Perform any action with the value


// update widget settings on ADA dashboard
  var url = 'https://ada.skynettechnologies.us/api/widget-setting-update-platform';
  var params = `u=${server_name}&widget_position=${icon_position}&platform_widget_status=${platform_widget_status}&is_widget_custom_position=${is_widget_custom_position}&is_widget_custom_size=${is_widget_custom_size}&widget_color_code=${colorcode}&widget_icon_type=${icon_type}&widget_icon_size=${icon_size}&widget_size=${widget_size}&widget_icon_size_custom=${widget_icon_size_custom}&widget_position_right=${widget_position_right}&widget_position_left=${widget_position_left}&widget_position_top=${widget_position_top}&widget_position_bottom=${widget_position_bottom}`;

  // Create the request
  var xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    if (xhr.status === 200) {
      alert('Settings updated successfully!');

    } else {
      alert('Error: Unable to update settings. Please try again.');

    }
  };

  xhr.onerror = function () {
    alert('Request failed. Please check your network connection.');
  };

  // Send the request with parameters
  xhr.send(params);




}




