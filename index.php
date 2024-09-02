<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
    }

    body {
      margin: 1rem;
    }

    :root {
      --input-height: 3lh;
      --rounded: 15px;
    }

    #db-name {
      height: 3hl;
      font-size: 1.5rem;
    }

    #terminal {
      width: 100%;
      height: 70vh;
      background-color: #000;
      color: #fff;
      font-size: 1.1rem;
      user-select: none;
      border: 1.5rem solid #000;
      border-radius: var(--rounded);
    }

    #terminal-prefix {
      width: 5rem;
      height: var(--input-height);
      line-height: 1lh;
      color: #fff;
      padding-left: 1rem;
      background-color: #000;
      display: flex;
      justify-content: center;
      align-items: center;
      border: none;
      border-radius: var(--rounded) 0 0 var(--rounded);
    }

    #terminal-input {
      width: 100%;
      height: var(--input-height);
      line-height: 1lh;
      background-color: #000;
      color: #fff;
      font-size: 1.2rem;
      outline: none;
      border: none;
      border-radius: 0 var(--rounded) var(--rounded) 0;
    }
  </style>
</head>

<body>
  <textarea name="" id="terminal" readonly></textarea>
  <div style="margin: 1rem 0;">
    <label for="db-name">DB_FILE_NAME</label><br />
    <input type="text" id="db-name">
  </div>
  <div style="display: flex;">
    <div id="terminal-prefix">sqlite>&nbsp;</div>
    <input type="text" id="terminal-input" onkeydown="enterKeyDown(event);">
  </div>

  <script>
    const terminal = document.querySelector("#terminal");
    const commandHis = [];
    let commandHisIndex = commandHis.length;

    function inputToTerminal(value) {
      if (terminal) {
        terminal.textContent = value;
      }
    }

    function enterKeyDown(e) {
      let dbName = document.querySelector("#db-name").value;
      // console.log(e.key)
      if (e.key === "Enter") {
        // DB入力漏れcheck
        if (dbName === "") {
          alert("DB name is Empty...");
          return;
        } else {
          if (dbName.split(".")[1] !== "db") {
            dbName += ".db";
          }
        }

        let command = e.target.value
        // 最後に;がなかったら補記する
        if (command !== "" && command.split("").pop() !== ";") {
          command += ";";
        }

        // terminalに入力コマンドを表示して、入力欄をリセットする
        terminal.textContent += `sqlite>  ${command}\n`;
        e.target.value = "";

        // コマンド履歴に記録する(commandHisIndexを最大値に戻す)
        if (command !== "") {
          commandHis.push(command);
        }
        commandHisIndex = commandHis.length;


        // コマンドが空欄でなかったら、phpにコマンドを送る
        const postData = {
          dbName: dbName,
          command: command
        }
        if (command !== "") {
          sendSql(postData);
        }

        async function sendSql(postData) {
          const res = await fetch("./db.php", {
            method: "POST",
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(postData)
          });
          const result = await res.text();
          getSqlResult(result);
        }

        // 結果をterminalに表示する
        function getSqlResult(result) {
          terminal.textContent += result + "\n" + "\n";
          // terminalエリアのスクロールを最下部にする
          terminal.scrollTop = terminal.scrollHeight;
        }

        // terminalエリアのスクロールを最下部にする
        terminal.scrollTop = terminal.scrollHeight;

      } else if (e.key === "ArrowUp" || e.key === "ArrowDown") {
        e.preventDefault();

        // 矢印キー入力のリアクションのため一瞬空欄にする
        e.target.value = "";
        setTimeout(() => {
          if (e.key === "ArrowUp") {
            commandHisIndex--;
            if (commandHis[commandHisIndex]) {
              e.target.value = commandHis[commandHisIndex];
            } else {
              commandHisIndex++;
            }
          } else if (e.key === "ArrowDown") {
            commandHisIndex++;
            if (commandHis[commandHisIndex]) {
              e.target.value = commandHis[commandHisIndex];
            } else {
              commandHisIndex--;
            }
          }
        }, 50);
      }

    }
  </script>
</body>

</html>