let url = "http://localhost/front/projet_de_la_meilleur_table/chat/say_me/"
let textEntries = document.querySelector("#textEntries")
let containerChat = document.querySelector(".containerChat")


/////// scroll en bas de la zone de message
function scrollToBottom() {
    containerChat.scrollTop = containerChat.scrollHeight;
}


///////////////// création les élement de  la section affichant le profil
function createProfilLeft() {

    let pp = document.createElement('div'),
        name = document.createElement('h3')
    pp.classList = "profilPicture"

    fetch(url + 'pseudo_user.php', {
        headers: new Headers(),
    })
        .then((res) => {
            return res.json()
        })
        .then((data) => {
            name.innerText = data['userName']
        })
        .catch(() => {
            console.log("Votre nom na pas pu etre chargé")
        })

    document.querySelector("#profilName").appendChild(name)


}

createProfilLeft()


let displayAllMessageChat = function (data) {
    while (containerChat.firstChild) {
        containerChat.removeChild(containerChat.firstChild);
    }
    /*
          data renvoie un tableau
          arrayAllMessage: Array(8)
          0: {user_content: "llll", user_color: "red", user_id: "1", user_pseudo: "richardD"}
          1: {user_content: "sam", user_color: "red", user_id: "1", user_pseudo: "richardD"}
          2: {user_content: "wala", user_color: "green", user_id: "3", user_pseudo: "leoL"}

         On prend sa taille   data.arrayAllMessage.length pour le parcourir

     */
    ////////// créer message par message le contenu du tableau
    for (let i = 0; i < data.arrayAllMessage.length; i++) {

        let userColor = data.arrayAllMessage[i]["user_color"]
        let messageContent = data.arrayAllMessage[i]["message_content"]
        let messageTime = data.arrayAllMessage[i]["message_time"]
        let userPseudo = data.arrayAllMessage[i]["user_pseudo"]
        let positionMessage = data.arrayAllMessage[i]['positionMessage']

        //// envoie  dans creatMessage tous le contenu d'un message
        creatMessage(userColor, messageContent, messageTime, userPseudo, positionMessage)

    }
    ///////////////  au chargement de page  affiche le premier message
    scrollToBottom()
}


/////////////// fonction de creation de message
let creatMessage = function (userColor, messageContent, messageTime, userPseudo, positionMessage) {

    let messageRow = document.createElement('div')
    let message = document.createElement('div')
    //   si le userId du message correspond au userId de l'utilisateur connecté, alors mettre le message a gauche sinon a droite
    //    ainsi chaque message de  l'utilisateur connecté sera tjr a droite

    if (positionMessage === "right") {
        messageRow.classList.add('rightMessage')
        message.classList.add('bubbleMe')
    } else {
        messageRow.classList.add('leftMessage')
        message.classList.add('bubbleThird')
    }

    let messagePseudo = document.createElement("span")
    messagePseudo.classList.add("pseudoMessage")
    messagePseudo.innerText = userPseudo

    let messageDate = document.createElement("span")
    messageDate.classList.add("dateMessage")
    messageDate.innerText = messageTime

    message.style.backgroundColor = (userColor)

    let para = document.createElement('p')
    para.innerText = messageContent

    message.appendChild(para)
    messageRow.appendChild(message)
    messageRow.appendChild(messagePseudo)
    messageRow.appendChild(messageDate)

    let mess = document.querySelector('.containerChat')
    mess.appendChild(messageRow)


    return mess

}

///// quand on appui sur entrée dans  le champs text  ca envoie a createNewMessage les valuers a inserer dans un message
textEntries.addEventListener('keypress', function (event) {
    if (event.keyCode === 13) {
        ////// previens le retour a la ligne
        event.preventDefault()
        if (textEntries.value === "") {
            return
        } else {
            createNewMessage(textEntries.value)
        }
    }

})
document.querySelector(".right > footer > div:nth-child(2)").addEventListener('click', function (event) {
    createNewMessage(textEntries.value)
})



/////////////// fonction qui rajoute un nouveau message en bd
let createNewMessage = function (valueMessage) {
    let objectMessage
    let channelId = document.querySelector('.containerChat').getAttribute("data-channel_id")

    objectMessage = {
        channelId: channelId,
        valueMessage: valueMessage,
    }

    fetch(url + 'create_message.php', {
        method: 'POST',
        headers: new Headers(),
        body: JSON.stringify(objectMessage)
    })
        .then((res) => {
            return res.json()
        })
        .then((data) => {
            ///// vide le champ text
            textEntries.value = ""
            ///// créer le message si tous c'est bien passé
            creatMessage(data.userColor, data.messageContent, data.messageTime, data.userPseudo, data.positionMessage)
            //////// scroll en bas pour qu'on puisse voir le dernier messag
            scrollToBottom()

        })

        .catch(() => {
            console.log("La création de votre message a échoué, veuillez réessayer ")
        })


}
////// fonction faisant la verif de nouveau message et de l'affiche de ceux ci

let displayUpdateMessage = function () {

    fetch(url + 'display_update_message.php', {
        headers: new Headers()
    })
        .then((res) => {
            return res.json()
        })
        .then((data) => {
            if (data.msg !== "") {
                //console.log(data.msg)
            }

            // console.log(data)

            console.log(data.arrayAllNewMessage[0]["messageTime"])

            for (let i = 0; i < data.arrayAllNewMessage.length; i++) {

                let userContent = data.arrayAllNewMessage[i]["user_content"]
                let userColor = data.arrayAllNewMessage[i]["user_color"]
                let userPseudo = data.arrayAllNewMessage[i]["user_pseudo"]
                let messageTime = data.arrayAllNewMessage[i]["messageTime"]
                let positionMessage = data.arrayAllNewMessage[i]['positionMessage']

                //// envoie  dans creatMessage tous le contenu d'un message
                creatMessage(userColor, userContent, userPseudo, messageTime, positionMessage)

            }
            scrollToBottom()

            return
        })

        .catch(() => {
            //console.log("impossible d'afficher de nouveaux articles")
        })
}

//setInterval(displayUpdateMessage, 2500)


// displayChannel()

// createChannelName("Channel principal", "", "1000")


let queryContentChannel = function (selectedChannel) {

    document.querySelector('.containerChat').setAttribute("data-channel_id", selectedChannel)

    fetch(url + 'selectedChannel.php', {
        method: 'POST',
        headers: new Headers(),
        body: JSON.stringify(selectedChannel)
    })
        .then((res) => {
            return res.json()
        })
        .then((data) => {

            displayAllMessageChat(data)
        })

        .catch((data) => {
            console.log("Le chargement de tous les channel a échoué")
        })


}
queryContentChannel("1000")

let creatListChannelUser = function (is_connected, pseudo) {

    let bodyLeftMenu = document.querySelector("#bodyLeftMenu")
    let userBlock = document.createElement("div")


    userBlock.classList = "userBlock"
    let userName = document.createElement("span")
    let userConnected = document.createElement("div")


    userConnected.classList = "userConnected"
    userConnected.style.background = is_connected
    userName.innerText = pseudo


    userBlock.appendChild(userName)
    userBlock.appendChild(userConnected)
    bodyLeftMenu.appendChild(userBlock)


}



let queryUserChannelList = function (selectedChannel) {


    fetch(url + 'userList.php', {
        method: 'POST',
        headers: new Headers(),
        body: JSON.stringify(selectedChannel)
    })
        .then((res) => {
            return res.json()
        })
        .then((data) => {

            for (let i = 0; i < data.arrayAllChatUsers.length; i++) {


                let is_connected = data.arrayAllChatUsers[i]["is_connected"]
                let pseudo = data.arrayAllChatUsers[i]["pseudo"]

                creatListChannelUser(is_connected, pseudo)
            }


        })

        .catch((data) => {
            console.log("Le chargement de tous les channel a échoué")
        })


}



queryUserChannelList(1000)



let bodyLeftMenu = document.querySelector("#bodyLeftMenu")

function createBtnAdd(){
let BtnAddChannel = document.createElement("span")
BtnAddChannel.classList.add("fas")
BtnAddChannel.classList.add("fa-plus-circle")
BtnAddChannel.style.cursor = 'pointer'
    BtnAddChannel.addEventListener('click', function () {
        if (form_channel.style.display === 'block') {
            form_channel.style.display = 'none'
            ChannelShadow.style.display = 'none'
        } else {
            form_channel.style.display = 'block'
            ChannelShadow.style.display = 'block'
        }
    })
bodyLeftMenu.appendChild(BtnAddChannel)
}

let form_channel = document.querySelector('#form_channel')
let ChannelShadow = document.querySelector('#shadow_channel')
let closeModal = document.querySelector('#closeModalChannel')

closeModal.addEventListener('click', function () {
    if (form_channel.style.display === 'block') {
        form_channel.style.display = 'none'
        ChannelShadow.style.display = 'none'
    } else {
        form_channel.style.display = 'block'
        ChannelShadow.style.display = 'block'
    }
});




function createChannelName(channel_description, channel_title, channel_id) {

    let channelContainer = document.createElement("div")
    channelContainer.classList = "channelContainer"


    let channelDescription = document.createElement("span")
    let channelTitle = document.createElement("h6")


    console.log(channel_title)


    channelTitle.innerText = channel_title

    channelDescription.innerText = channel_description

    channelContainer.appendChild(channelTitle)
    channelContainer.appendChild(channelDescription)




    channelContainer.setAttribute("data-channel_id", channel_id)

    bodyLeftMenu.appendChild(channelContainer)


    channelContainer.addEventListener("click", function () {
        queryContentChannel(this.getAttribute("data-channel_id"))
        while (bodyLeftMenu.firstChild) {
            bodyLeftMenu.removeChild(bodyLeftMenu.firstChild);
        }
        document.querySelector("#channelList").style.display = "block"





       // queryUserChannelList()

    })

}

let UpdateDisplayChannel = function(){

    fetch(url + 'update_display_channel.php', {
        headers: new Headers(),
    })
        .then((res) => {
            return res.json()
        })
        .then((data) => {
            console.log(data)

            for (let i = 0; i <= data.arrayAllUserChannel.length - 1; i++) {

                let channel_description = data.arrayAllUserChannel[i]["channel_description"]
                let channel_title = data.arrayAllUserChannel[i]["channel_title"]
                let channel_id = data.arrayAllUserChannel[i]["channel_id"]

                createChannelName(channel_description, channel_title, channel_id)
            }
        })

        .catch((data) => {
            console.log("Le chargement de tous les channel a échoué")
        })



}
document.querySelector("#channelList").addEventListener("click", function () {
    displayChannel()

setInterval(function () {
    UpdateDisplayChannel()
}, 2500)

    document.querySelector("#channelList").style.display = "none"

    while (bodyLeftMenu.firstChild) {
        bodyLeftMenu.removeChild(bodyLeftMenu.firstChild);
    }

})

let displayChannel = function () {

    fetch(url + 'display_channel.php', {
        headers: new Headers(),
    })
        .then((res) => {
            return res.json()
        })
        .then((data) => {
            createBtnAdd()
            for (let i = 0; i <= data.arrayAllUserChannel.length - 1; i++) {

                let channel_description = data.arrayAllUserChannel[i]["channel_description"]
                let channel_title = data.arrayAllUserChannel[i]["channel_title"]
                let channel_id = data.arrayAllUserChannel[i]["channel_id"]

                createChannelName(channel_description, channel_title, channel_id)
            }
        })

        .catch((data) => {
            console.log("Le chargement de tous les channel a échoué")
        })
}


queryContentChannel("1000")


let menu = document.querySelector('#menu')

document.querySelector('#crossContainer').addEventListener('click', function () {
    menu.classList.replace("menu", "menuOpen")
})

document.querySelector('#closeMenu').addEventListener('click', function () {
    menu.classList.replace("menuOpen", "menu")
    menu.style.animation = 'slideOut 700ms ease-in-out'
})
document.querySelector("#modify").addEventListener("click",function () {
    menu.classList.replace("menuOpen", "menu")
    menu.style.animation = 'slideOut 700ms ease-in-out'
})





let modifi = document.querySelector('#modify')
let formEdtion = document.querySelector('#test-form')
let blackShadow = document.querySelector('#shadow')
let closeModalUpdate = document.querySelector('#closeModal')

closeModalUpdate.addEventListener('click', function () {
    if (formEdtion.style.display === 'block') {
        formEdtion.style.display = 'none'
        blackShadow.style.display = 'none'
    } else {
        formEdtion.style.display = 'block'
        blackShadow.style.display = 'block'
    }
});

modifi.addEventListener('click', function () {
    if (formEdtion.style.display === 'block') {
        formEdtion.style.display = 'none'
        blackShadow.style.display = 'none'
    } else {
        formEdtion.style.display = 'block'
        blackShadow.style.display = 'block'
    }
});

let register = function () {
    fetch(url + 'recupInfo.php', {
        method: 'POST',
        headers: new Headers(),
        body: new FormData(formEdtion)
    }).then((res) => res.json())
        .then((data) => {
            let alert = document.querySelector('#alert')
            alert.innerText = data.msg
            if (data.type === 0) {
                alert.classList.remove('success')
                alert.classList.add('failed')
            } else {
                alert.classList.remove('failed')
                document.location.reload(true)
            }
            alert.style.display = "block"
            alert.style.paddingTop = '10px'
            alert.style.fontFamily = 'sans-serif'
        })
}

let newChannel = function () {
    fetch(url + 'add_channel.php', {
        method: 'POST',
        headers: new Headers(),
        body: new FormData(form_channel)
    }).then((res) => res.json())
        .then((data) => {
            let alert = document.querySelector('#alert2')
            alert.innerText = data.msg
            if (data.type === 0) {
                alert.classList.remove('success')
                alert.classList.add('failed')
            } else {
                alert.classList.remove('failed')
                document.location.reload(true)
            }
            alert.style.display = "block"
            alert.style.paddingTop = '10px'
            alert.style.fontFamily = 'sans-serif'
        })
}

formEdtion.addEventListener('submit', function (e) {
    e.preventDefault()
    register()
})

form_channel.addEventListener('submit', function (e) {
    e.preventDefault()
    newChannel()
})

document.querySelector('#modify').addEventListener('click', function () {


    fetch(url + 'value_edit.php', {
        headers : new Headers(),
    }).then((res) => res.json())
        .then((data) => {
            document.querySelector('#name').value = data.name
            document.querySelector('#firstname').value = data.firstname
            document.querySelector('#nickname').value = data.pseudo
            document.querySelector('#mail').value = data.email
            //document.querySelector('#color').value = data.color
        })
})
let selectUsers = document.querySelector('#users')

let createSltUsers = function (a, b) {

    let opt = document.createElement('option')
    opt.value = a
    opt.innerText = b

    selectUsers.appendChild(opt)

}

let querryUSers = function() {
    fetch(url + 'users.php', {
        headers : new Headers(),
    }).then((res) => res.json())
        .then((data) => {
            console.log(data)
            for (let i = 0; i < data.arrayChatUsers.length; i++) {

                let userId = data.arrayChatUsers[i]["userId"]
                let pseudo = data.arrayChatUsers[i]["pseudo"]
                console.log(pseudo)

                createSltUsers(userId, pseudo)
            }

        })
}

querryUSers()









