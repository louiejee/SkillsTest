-- Database: election

-- Table: Positions
CREATE TABLE IF NOT EXISTS Positions (
    posID INT AUTO_INCREMENT PRIMARY KEY,
    posName VARCHAR(100) NOT NULL,
    numOfPositions INT NOT NULL,
    posStat ENUM('open', 'closed') DEFAULT 'open'
);

-- Table: Candidates
CREATE TABLE IF NOT EXISTS Candidates (
    candID INT AUTO_INCREMENT PRIMARY KEY,
    candFName VARCHAR(50) NOT NULL,
    candMName VARCHAR(50),
    candLName VARCHAR(50) NOT NULL,
    posID INT,
    candStat ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (posID) REFERENCES Positions(posID)
);

-- Table: Voters
CREATE TABLE IF NOT EXISTS Voters (
    voterID VARCHAR(50) PRIMARY KEY,
    voterPass VARCHAR(255) NOT NULL,
    voterFName VARCHAR(50) NOT NULL,
    voterMName VARCHAR(50),
    voterLName VARCHAR(50) NOT NULL,
    voterStat ENUM('active', 'inactive') DEFAULT 'active',
    voted ENUM('y', 'n') DEFAULT 'n'
);

-- Table: Votes
CREATE TABLE IF NOT EXISTS Votes (
    posID INT,
    voterID VARCHAR(50),
    candID INT,
    PRIMARY KEY (posID, voterID),
    FOREIGN KEY (posID) REFERENCES Positions(posID),
    FOREIGN KEY (voterID) REFERENCES Voters(voterID),
    FOREIGN KEY (candID) REFERENCES Candidates(candID)
);