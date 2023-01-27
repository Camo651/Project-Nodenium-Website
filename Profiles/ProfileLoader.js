
//#region Helper Functions
function imageExists(image_url){

    try{
        var http = new XMLHttpRequest();
        http.open('HEAD', image_url, false);
        http.send();
        return http.status!=404;
    }catch(e){
        return false;
    }
  }

function tryLoadImage(path){
    if(imageExists(path)){
      return path;
    } else {
      return "https://projectnodenium.com/images/m.png";
    }
  }

loadData();

function shuffle(array) {
  let currentIndex = array.length,  randomIndex;
  while (currentIndex != 0) {
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex--;
    [array[currentIndex], array[randomIndex]] = [
      array[randomIndex], array[currentIndex]];
  }

  return array;
}

//#endregion

let profileNames = [];
let profiles = new Map();

async function loadData(){
    v = Math.random();
    console.log("Fetching v: "+v);
    let namesResp = await fetch('https://projectnodenium.com/Profiles/MemberNames.json?v='+v);
    let names = await namesResp.json();
    profileNames = names;
    for(let i = 0; i < profileNames.length; i++){
        let respose = await fetch('https://projectnodenium.com/Profiles/ProfileDatas/'+profileNames[i]+'_profile.json?v='+v);
        let data = await respose.json();
        profiles.set(profileNames[i], data);
        console.log("Loaded: "+profileNames[i]);
    }
    console.log("Member data successfully loaded");
    useData();
}

