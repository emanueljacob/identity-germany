# German Passport validation (Reisepass)
This package descripes the validation process of the german passport
(Reisepass).

![passport example](../../../art/passport_mustermann_marked.jpg)
The id card is build from 4 different strings

## ID cart string structure
The complete calculation procedure of the id validation can be described like this.


The following structure explains the parts that the id card string will be divided into.

- Serialnumber (based on authority code + consecutive number + checksum): 4 mixed characters + 4 mixed characters + 1 digit<br>
  Example: `C01X0006H1D` with<br>
  - Authority code (Behördenkennzahl; BKZ) ID: 4 mixed characters (`[A-Z0-9]{4}`)<br>
    Example: `C01X`
  - Consecutive number: 5 digits<br>
    Example: `0006H`
  - Checksum: 1 digit<br>
    Example: `1`
- Birth (birth date) + checksum: 6 digits + 1 digit<br>
  Example: `6408125` with<br>
    - `64` is the year of birth
    - `08` is the month of birth 
    - `12` is the day of birth 
    - `5` is the checksum
- Gender 1 uppercase character for the gener (`F` = female; `M` = male)<br>
  Example: `F`
- Expiry (expire date) + checksum: 6 digits + 1 digit<br>
  Example: `2010315` with<br>
    - `17` is the year of expiry
    - `10` is the month of expiry
    - `31` is the day of expiry
    - `9` is the checksum
- Nationality: 1 uppercase character, usually `D` for "**D**eutschland" (= "Germany")<br>
  Example: `D`
- Total checksum: 1 digit<br>
  The checksum, with reference to all parts as explained above
  Example: `0`


## Further reading

### Insights
- [Behördenkennzahl; BKZ (external website)](http://www.pruefziffernberechnung.de/Begleitdokumente/BKZ.shtml)
- [Behördenkennzahl; BKZ (external website)](http://www.pruefziffernberechnung.de/Begleitdokumente/BKZ.shtml)

### Generating random Personalausweis numbers (testing)
- [perso.xyz (external website)](https://www.perso.xyz/)
- [Calculation of the german idcard checksums (external website)](http://www.pruefziffernberechnung.de/P/Personalausweis-DE.shtml)
