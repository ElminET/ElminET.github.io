const cursor = document.querySelector(".cursor");
const holes = document.querySelectorAll(".hole");
const scoreEl = document.getElementById("score");
const startBtn = document.getElementById("startBtn");
const timerEl = document.getElementById("timer");

const smashSound = new Audio("assets/smash.mp3");
const evilSound  = new Audio("assets/evil-sounds-laugh.mp3");
const levelSound = new Audio("assets/levelup.mp3");

let score = 0;
let time = 30;
let running = false;
let timer;

let goldActive = false;
let evilActive = false;

function updateScore(points){
  score += points;
  scoreEl.textContent = "SCORE: " + score;
}

function spawn(hole, img, points, sound, life, onRemove, extraClass){
  const mole = document.createElement("img");
  mole.src = img;
  mole.className = extraClass ? "mole " + extraClass : "mole";

  function remove(){
    mole.remove();
    if(onRemove) onRemove();
  }

  const timeout = setTimeout(remove, life);

  mole.addEventListener("mousedown", () => {
    clearTimeout(timeout);
    updateScore(points);
    sound.currentTime = 0;
    sound.play();
    setTimeout(remove, 200);
  });

  hole.appendChild(mole);
}

function run(){
  if(!running) return;

  const hole = holes[Math.floor(Math.random() * holes.length)];

  spawn(hole, "assets/mole.png", 10, smashSound, 1500, run);

  if(!goldActive && Math.random() < 0.14){
    goldActive = true;
    spawn(
      hole,
      "assets/gold-mole.png",
      50,
      levelSound,
      900,
      () => goldActive = false,
      "gold"
    );
  }

  if(!evilActive && Math.random() < 0.21){
    evilActive = true;
    spawn(
      hole,
      "assets/evil-mole.png",
      -30,
      evilSound,
      1700,
      () => evilActive = false
    );
  }
}

function startGame(){
  clearInterval(timer);

  score = 0;
  time = 30;
  goldActive = false;
  evilActive = false;
  running = true;

  scoreEl.textContent = "SCORE: 0";
  timerEl.textContent = "TIME: " + time;
  startBtn.textContent = "RESTART";

  run();
  run();

  timer = setInterval(() => {
    time--;
    timerEl.textContent = "TIME: " + time;
    if(time <= 0) endGame();
  }, 1000);
}

function endGame(){
  running = false;
  clearInterval(timer);
  alert("GAME OVER\nScore: " + score);
}

startBtn.addEventListener("click", startGame);

window.addEventListener("mousemove", e => {
  cursor.style.left = e.pageX + "px";
  cursor.style.top  = e.pageY + "px";
});

window.addEventListener("mousedown", () => {
  cursor.classList.add("active");
});

window.addEventListener("mouseup", () => {
  cursor.classList.remove("active");
});