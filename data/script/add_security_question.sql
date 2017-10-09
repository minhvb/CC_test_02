TRUNCATE TABLE security_question;
INSERT INTO security_question(content, createDate, updateDate) VALUES
('卒業した小学校は？（･･･市立〇〇小学校）', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('高校の最寄駅は？（〇〇駅）', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('最も苦手な食べ物は？', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('最初の勤務地の最寄駅は？（〇〇駅）', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('初めての海外旅行で訪れた国は？', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('初めて買ったCDの歌手の名前は？', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())