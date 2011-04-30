class Graph {

  float startX = width*.05;
  float endX = width- (width*.05);
  float startY = height*.05;
  float endY = height - (height*.3);
  Data[] data;
  Theme[] themes;
  int numThemes;
  String[] labels;
  float maxVal;
  float[] startingVals;

  float Yspacing;
  float Xinterval;

  public Graph(float[] _startingVals, float _max, Data[] _data, String[] _labels) {
    maxVal = _max;
    startingVals = _startingVals;
    data = _data;
    labels = _labels;
    Yspacing = ((endY*.9)-startY)/maxVal;
    Xinterval = (endX-startX)/(labels.length-2);
    themes = new Theme[data.length];
    numThemes = data.length;
  }

  private void setupGraph() {

    float[] values;
    values = new float[startingVals.length];

    for(int i=0; i<startingVals.length; i++) {
      values[i] = startingVals[i];
    }

    themes[0] = new Theme(0, data[0], startingVals, startX, endX, endY, Yspacing, Xinterval);
    int r = (int)random(0, 255);
    int g = (int)random(0, 255);
    int b = (int)random(0, 255);
    themes[0].setColor (r, g, b);

    for(int j = 0; j< data.length-1; j++) {

      float[] temp_values = new float[values.length];

      for(int i=0; i<values.length; i++) {
        temp_values[i] = values[i];
      }

      for(int i=0; i<temp_values.length; i++) {

        float currentVal =data[j].values[i];
        temp_values[i] -= currentVal;
        values[i] -= currentVal;
      }
      themes[j+1] = new Theme(j+1, data[j+1], temp_values, startX, endX, endY, Yspacing, Xinterval);
      int r2 = (int)random(0, 255);
      int g2 = (int)random(0, 255);
      int b2 = (int)random(0, 255);
      themes[j+1].setColor (r2, g2, b2);
    }
  }


  private void drawIntervals() {
    
    float i = startX;
    int k = 1;
    while(i <= endX) {
     stroke(0);
     strokeWeight(3);
       line(i, endY+10, i, endY+20);
     
       fill(0);
       textSize(10);
       textAlign(CENTER,TOP);
       text(labels[k++], i, endY+25);
         i += Xinterval; 
    }
    
    strokeWeight(1);
    stroke(0);
  }

  private int findNearestInterval(int mousex) {
        
    float i = startX;
    int k = 0;
    while(i <= endX) {
         if((mousex > (i-Xinterval/2)) && (mousex <= (i+Xinterval/2))) return k;
         i += Xinterval; 
         k++;
    }
    return k;
  }


  private void drawGraph() {
    rectMode(CORNERS);
/*    fill(200);
    rect(startX,startY,endX,endY);*/
    drawIntervals();
    for(int i = 0; i< numThemes; i++) {  
      if (themes[i].getSelected() == true) {
        themes[i].renderSelected();
        int loc = findNearestInterval(mouseX);
        themes[i].drawLabel(loc, labels[loc+1]);
      }
      else {
        themes[i].render();
      }
    }
  }



  private void drawPickBuffer() {
    pickbuffer.beginDraw();
    pickbuffer.background(255);
    for (int i=0; i<numThemes; i++) { 
      themes[i].renderIsect(pickbuffer);
    }
    pickbuffer.endDraw();
  }

  private void checkThemes() {

    drawPickBuffer();
    int isectColor = pickbuffer.get(mouseX,mouseY);
    for (int i=0; i<numThemes; i++) { 
      if (themes[i].isect((int)red(isectColor), (int)green(isectColor), (int)blue(isectColor)) == true) {
        themes[i].setSelected(true);
      }
      else {
        themes[i].setSelected(false);
      }
    }
  }
}



class Theme {

  int id;
  Data data;
  float[] values;
  boolean selected = false;
  int r, g, b;

  float startX;
  float endX;
  float baseline;
  float Yspacing;
  float Xinterval;

  private Theme(int _id, Data _data, float[] _values, float _startX, float _endX, 
  float _base, float _spacing, float _interval) {
    id = _id;
    data = _data;
    values = _values; 
    startX = _startX;
    endX = _endX;
    baseline = _base;
    Yspacing = _spacing;
    Xinterval = _interval;
  }

  private void drawTheme() { 
    beginShape();
    vertex(startX, baseline);
    vertex(startX,  baseline-values[0]*Yspacing);
    float q = startX;
    curveVertex(q, baseline-values[0]*Yspacing);
    for(int i=0; i<values.length; i++) {
      curveVertex(q,baseline-values[i]*Yspacing); 
      q += Xinterval;
    }
    int l2 = values.length-1;
    curveVertex(endX,  baseline-values[l2]*Yspacing);
    vertex(endX,  baseline-values[l2]*Yspacing);
    vertex(endX, baseline);
    endShape(CLOSE);
  }

  private void drawLabel(int index, String time) {
  
    textAlign(CENTER,BOTTOM);
    textSize(40);
    fill(0);
    text(data.name + ", " + time + ", " + int(data.values[index]), ((endX-startX)/2) + startX, height-(height*.07));

  }
  
  

  private void setColor(int _r, int _g, int _b) {
    r = _r; 
    g = _g; 
    b = _b;
  }

  public boolean getSelected () {
    return selected;
  }

  public void setSelected (boolean _selected) {
    selected = _selected;
  }  

  private void render() {
    fill(r,g,b);
    drawTheme();
  }

  private void renderSelected() {
    fill(r+50,g+50,b+50);
    drawTheme();
  }  

  public void renderIsect(PGraphics pg) {
    pg.fill(red(id), green(id), blue(id));
    pg.beginShape();
    pg.vertex(startX, baseline);
    pg.vertex(startX,  baseline-values[0]*Yspacing);
    float q = startX;
    pg.curveVertex(q, baseline-values[0]*Yspacing);
    for(int i=0; i<values.length; i++) {
      pg.curveVertex(q,baseline-values[i]*Yspacing); 
      q += Xinterval;
    }
    int l2 = values.length-1;
    pg.curveVertex(endX,  baseline-values[l2]*Yspacing);
    pg.vertex(endX,  baseline-values[l2]*Yspacing);
    pg.vertex(endX, baseline);
    pg.endShape(CLOSE);
  }

  public boolean isect (int _r, int _g, int _b) {
    if ((red(id) == _r) && (green(id) == _g) && (blue(id) == _b)) {
      return true;
    }
    return false;
  }
}

