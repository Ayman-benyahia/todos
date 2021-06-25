document.body.onclick = (event) => {
    if(event.target.className === "task__mark") {
        console.log(event.target);
        event.target.parentElement.submit();
    }
}