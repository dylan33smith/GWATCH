import React from "react";

function App() {
  const colors = ["#416BEC", "#888", "#D04F35", "#309554", "#F5B90F", "#888"];
  const letters = "GWATCH";

  return (
    <div>
      {/* Top Nav Bar */}
      <nav style={styles.navbar}>
        <div style={styles.logo}>
          {letters.split("").map((letter, index) => (
            <span key={index} style={{ color: colors[index] }}>
              {letter}
            </span>
          ))}
        </div>
        <ul style={styles.navLinks}>
          <li>What GWATCH does</li>
          <li>Features of GWATCH</li>
          <li>Tutorial</li>
          <li>Active Datasets</li>
        </ul>
      </nav>

      {/* Main Centered Section */}
      <div style={styles.hero}>
        <h1 style={styles.heroTitle}>
          {letters.split("").map((letter, index) => (
            <span key={index} style={{ color: colors[index] }}>
              {letter}
            </span>
          ))}
        </h1>
        <h2 style={styles.heroSubtitle}>
          Genome-Wide Association Tracks Chromosome Highway
        </h2>
      </div>
    </div>
  );
}

const styles = {
  navbar: {
    display: "flex",
    justifyContent: "space-between",
    alignItems: "center",
    padding: "1rem 2rem",
    backgroundColor: "#f7f7f7",
    borderBottom: "1px solid #ddd",
  },
  logo: {
    fontSize: "1.8rem",
    fontWeight: "bold",
    fontFamily: "Arial, sans-serif",
  },
  navLinks: {
    display: "flex",
    listStyleType: "none",
    gap: "2rem",
    fontFamily: "Arial, sans-serif",
    fontSize: "1rem",
    margin: 0,
    padding: 0,
  },
  hero: {
    height: "80vh",
    display: "flex",
    flexDirection: "column",
    justifyContent: "center",
    alignItems: "center",
    fontFamily: "Georgia, serif",
    textAlign: "center",
  },
  heroTitle: {
    fontSize: "5rem",
    marginBottom: "1rem",
  },
  heroSubtitle: {
    fontSize: "1.5rem",
    fontWeight: "normal",
  },
};

export default App;
