    function showContent(type) {
      let content = "";

      if (type === "vision") {
        content = "A globally competitive LGU Office that empowers Imuseños to achieve economic vitality and quality of life.";
    } else if (type === "mission") {
        content = `The City of Imus Cooperative, Livelihood & Entrepreneurial Development Office shall:

        Foster the creation and growth of cooperatives and entrepreneurs towards socio-economic development by increasing economic opportunities and benefits for Imuseños.
        Improve the livelihood of Imuseños through the creation of conducive business opportunities.
        Provide professional, quality and responsive technical and financial assistance to clients in developing viable and responsive businesses.`;
      } else if (type === "goal") {
        content = "Our goal is to improve community welfare, enhance infrastructure, and foster sustainable growth.";
      }

      document.getElementById("content-text").textContent = content;
    }


    document.addEventListener("DOMContentLoaded", function () {
        AOS.init();
      });
      
      //date and time.
      function updateDateTimePH() {
        const options = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true };
        const dateTimePH = new Date().toLocaleString('en-PH', options);
        document.getElementById('dateTimePH').textContent = dateTimePH;
      }
      setInterval(updateDateTimePH, 1000);
      updateDateTimePH();