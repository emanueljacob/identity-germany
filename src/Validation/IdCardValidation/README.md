# German Identity Card validation (Neuer Personalausweis/Alter Personalausweis)
This package describes the validation process of the german identity card
(neuer deutscher Personalausweis/alter deutscher Personalausweis).

![old idcard example](../../../art/idcard-old_mustermann_marked.jpeg)
![new idcard example](../../../art/idcard-new_mustermann_marked.jpeg)

The document is build from different strings:

## String structure
The complete calculation procedure of the id validation can be described like this.

The following structure explains the parts that the id card string will be divided into.

- Serialnumber (based on authority code + consecutive number + checksum): 4 mixed characters + 4 mixed characters + 1 digit<br>
  Example: `T220001293` with<br>
  - Authority code (Behördenkennzahl; BKZ) ID: 4 mixed characters (`[A-Z0-9]{4}`)<br>
    Example: `T220`
  - Consecutive number: 5 digits<br>
    Example: `00129`
  - Checksum: 1 digit<br>
    Example: `3`
- Birth (birth date) + checksum: 6 digits + 1 digit<br>
  Example: `6408125` with<br>
    - `64` is the year of birth
    - `08` is the month of birth 
    - `12` is the day of birth 
    - `5` is the checksum
- Expiry (expire date) + checksum: 6 digits + 1 digit<br>
  Example: `2010315` with<br>
    - `20` is the year of expiry
    - `10` is the month of expiry
    - `31` is the day of expiry
    - `5` is the checksum
- Nationality: 1 uppercase character, usually `D` for "**D**eutschland" (= "Germany")<br>
  Example: `D`
- Total checksum: 1 digit<br>
  The checksum, with reference to all parts as explained above
  Example: `4`


## Further reading

### Insights
- [Behördenkennzahl; BKZ (external website)](http://www.pruefziffernberechnung.de/Begleitdokumente/BKZ.shtml)

### Generating random id card (Personalausweis) numbers (testing)
- [perso.xyz (external website)](https://www.perso.xyz/)
- [Calculation of the german id card checksums (external website)](http://www.pruefziffernberechnung.de/P/Personalausweis-DE.shtml)
