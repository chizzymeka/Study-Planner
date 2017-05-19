/*Validator for the login form*/
function loginValidator() {

    var flag = false;

    //If email is provided, check whether it is valid, check whether it is empty, and finally, check whether it is not valid
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('username').value)) {
        flag = true;
    } else if (document.getElementById('username').value === "") {
        document.getElementById('username').style.borderColor = "red";
        alert('Please enter your email');
        flag = false;
        return false;
    } else if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('username').value))) {
        document.getElementById('username').style.borderColor = "red";
        alert('You have entered an invalid email address!');
        flag = false;
        return false;
    }

    if (document.getElementById('password').value === "") {
        document.getElementById('password').style.borderColor = "red";
        alert('Please enter your password ');
        flag = false;
        return false;
    }

    // if flag remains true, then submit form
    if (flag) {
        return true;
        alert('Your account has been created!');
        // else, don't submit form
    } else {
        return false;
    }
}

/*Validator for the registration form*/
function registrationValidator() {

    var mobilePattern = /^\d{11}$/;
    var flag = false;

    // if the field is not empty set flag to true
    if (document.getElementById('firstName').value !== "") {
        flag = true;
        // Don't submit the form if firstname is missing
    } else if (document.getElementById('firstName').value === "") {
        document.getElementById('firstName').style.borderColor = "red";
        alert('Please enter your first name');
        flag = false;
        return false;
    }

    // if the field is not empty set flag to true
    if (document.getElementById('lastName').value !== "") {
        flag = true;
        // Don't submit the form if lastname is missing
    } else if (document.getElementById('lastName').value === "") {
        document.getElementById('lastName').style.borderColor = "red";
        alert('Please enter your last name');
        flag = false;
        return false;
    }

    // if the field is not empty set flag to true
    if (document.getElementById('studentId').value !== "") {
        flag = true;
        // Don't submit the form if student ID is missing
    } else if (document.getElementById('studentId').value === "") {
        document.getElementById('studentId').style.borderColor = "red";
        alert('Please enter your student ID');
        flag = false;
        return false;
    }

    // if mobile patter matches the regex, return true
    if (document.getElementById('mobile').value.match(mobilePattern)) {
        flag = true;
        // Don't submit the form if lastname is missing
    } else if (document.getElementById('mobile').value === "") {
        flag = true;
        // don't submit if the mobile patter does not match
    } else if ((document.getElementById('mobile').value.match(mobilePattern)) === null) {
        document.getElementById('mobile').style.borderColor = "red";
        alert('You have entered an invalid UK mobile number!');
        flag = false;
        return false;
    }

    //If email is provided, check whether it is valid, check whether it is empty, and finally, check whether it is not valid
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('username').value)) {
        flag = true;
    } else if (document.getElementById('username').value === "") {
        document.getElementById('username').style.borderColor = "red";
        alert('Please enter your email');
        flag = false;
        return false;
    } else if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('username').value))) {
        document.getElementById('username').style.borderColor = "red";
        alert('You have entered an invalid email address!');
        flag = false;
        return false;
    }

    // Firstly, check if password is empty, then check if password confirmation is empty, then check if password is equal to its confirmation, then check if it is not equal to its confirmation
    if (document.getElementById('password').value === "") {
        document.getElementById('password').style.borderColor = "red";
        alert('Please enter your password');
        flag = false;
        return false;
    } else if (document.getElementById('confirmPassword').value === "") {
        document.getElementById('confirmPassword').style.borderColor = "red";
        alert('Please re-enter your password');
        flag = false;
        return false;
    } else if (document.getElementById('password').value === (document.getElementById('confirmPassword').value)) {
        flag = true;
    } else if (document.getElementById('password').value !== (document.getElementById('confirmPassword').value)) {
        document.getElementById('password').style.borderColor = "red";
        document.getElementById('confirmPassword').style.borderColor = "red";
        alert('Passwords do not match!');
        flag = false;
        return false;
    }

    // if flag remains true, then submit form
    if (flag) {
        return true;
        alert('Your account has been created!');
        // else, don't submit form
    } else {
        return false;
    }
}

/*Validator for the study plan form*/
function studyPlanValidator() {
    var errorMessage = "";

    if (document.getElementById('moduleStudyDate').value === "") {
        errorMessage += 'Please select a study date \n';
        document.getElementById('moduleStudyDate').style.borderColor = "red";
        alert(errorMessage);
        return false;
    }

    if (errorMessage !== "") {
        alert(errorMessage);
        return false;
    }
}

/*Validator for the change password form*/
function changePasswordValidator() {

    var flag = false;

    if (document.getElementById('currentPassword').value === "") {
        document.getElementById('currentPassword').style.borderColor = "red";
        alert('Please enter your current password');
        flag = false;
        return false;
    } else if (document.getElementById('newPassword').value === "") {
        document.getElementById('newPassword').style.borderColor = "red";
        alert('Please enter your new password');
        flag = false;
        return false;
    } else if (document.getElementById('confirmNewPassword').value === "") {
        document.getElementById('confirmNewPassword').style.borderColor = "red";
        alert('Please confirm your new password');
        flag = false;
        return false;
    } else if ((document.getElementById('currentPassword').value !== "") & (document.getElementById('newPassword').value === (document.getElementById('confirmNewPassword').value))) {
        alert('Your password has been changed!');
        flag = true;
        return true;
    } else if ((document.getElementById('currentPassword').value !== "") & (document.getElementById('newPassword').value !== (document.getElementById('confirmNewPassword').value))) {
        document.getElementById('newPassword').style.borderColor = "red";
        document.getElementById('confirmNewPassword').style.borderColor = "red";
        alert('Passwords do not match!');
        flag = false;
        return false;
    }
    
    // if flag remains true, then submit form
    if (flag) {
        return true;
        alert('Your password has been changed!');
        // else, don't submit form
    } else {
        return false;
    }
}

/*Validator for the add modules form*/
function addModuleValidator() {
    var errorMessage = "";

    if (document.getElementById('moduleCode').value === "") {
        errorMessage += 'Please enter the module code \n';
        document.getElementById('moduleCode').style.borderColor = "red";
        alert(errorMessage);
        return false;
    }

    if (document.getElementById('moduleTitle').value === "") {
        errorMessage += 'Please enter the module title \n';
        document.getElementById('moduleTitle').style.borderColor = "red";
        alert(errorMessage);
        return false;
    }

    if (errorMessage !== "") {
        alert(errorMessage);
        return false;
    }

    /*Success confirmation message*/
    if (errorMessage === "") {
        alert('The module has been added!');
        return true;
    }
}